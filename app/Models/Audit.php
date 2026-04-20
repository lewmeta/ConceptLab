<?php

namespace App\Models;

use App\Enums\AuditStatus;
use App\Models\Concerns\HasWorkspaceScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['workspace_id', 'user_id', 'parent_audit_id', 'version', 'title', 'input_text', 'input_char_count', 'input_word_count', 'status', 'sharpness_score', 'is_demo', 'demo_session_key', 'demo_session_expires', 'claimed_at', 'ai_safety_net_completed', 'analyzing_started_at', 'diagnosed_at', 'sharpened_at',])]
class Audit extends Model
{
    use HasFactory, SoftDeletes, HasWorkspaceScope;

    protected function casts(): array
    {
        return [
            'status' => AuditStatus::class,
            'sharpness_score' => 'decimal:2',
            'is_demo' => 'boolean',
            'ai_safety_net_completed' => 'boolean',
            'demo_session_expires' => 'datetime',
            'claimed_at' => 'datetime',
            'analyzing_started_at' => 'datetime',
            'diagnosed_at' => 'datetime',
            'sharpened_at' => 'datetime',
        ];
    }

    // ─── PHP 8.4 Property Hooks ───────────────────────────────────────────

    /** Whether this audit has been claimed by a registered user after the demo */
    public bool $isClaimed {
        get => $this->claimed_at !== null;
    }

    /**
     * Whether this is an unclaimed demo audit whose session has expired.
     * Expired demos are eligible for cleanup by the scheduled job.
     */
    public bool $isDemoExpired {
        get => $this->is_demo
            && $this->demo_session_expires !== null
            && $this->demo_session_expires->isPast();
    }

    /** Whether this is the first version in its version chain (no parent) */
    public bool $isRootVersion {
        get => $this->parent_audit_id === null;
    }

    // ─── Immutability and State Enforcement ──────────────────────────────

    protected static function booted(): void
    {
        // Derive char and word counts from input_text at creation time.
        // These are computed values, not user-supplied - the model owns them. 
        static::creating(function (Audit $audit) {
            $audit->input_char_count = mb_strlen($audit->input_text);
            $audit->input_word_count = str_word_count(strip_tags($audit->input_text));
        });

        // input_text is frozen at INSERT - editing creates a new version row.
        static::updating(function (Audit $audit) {
            if ($audit->isDirty('input_text')) {
                throw new \LogicException(
                    "Audit #{$audit->id} input_text is immutable." .
                        "Create a new version via Audit::newVersion() instead."
                );
            }
        });

        // Status transitions are forward-only: draft → analyzing → diagnosed → sharpened.
        static::updating(function (Audit $audit) {
            if (! $audit->isDirty('status')) {
                return;
            }

            $from = AuditStatus::from($audit->getOriginal('status'));
            $to = $audit->status;

            if (! $from->canTransitionTo($to)) {
                throw new \LogicException(
                    "Invalid audit status transition: [{$from->value}] → [{$to->value}]. " .
                        "Status moves forward only."
                );
            }
        });
    }


    // ─── Domain Actions ───────────────────────────────────────────────────

    /**
     * Create a new version of this audit with revised input text
     * 
     * The original audit is permanently preserved as a forensic record.
     * The new version links back via parent_audit_id and increments version.
     * char/word counts are derived automatically in the creating hook.
     */
    public function newVersion(string $newInputText): static
    {
        return static::create([
            'workspace_id' => $this->workspace_id,
            'user_id' => $this->user_id,
            'parent_audit_id' => $this->id,
            'version' => $this->version + 1,
            'title' => $this->title,
            'input_text' => $newInputText,
            'status' => AuditStatus::Draft,
            'is_demo' => false,
        ]);
    }

    /**
     * Advance the audit to the next permitted status in the state machine
     * 
     * Stamps the corresponding lifecycle timestamp column automatically
     * Throws if the audit is already at the terminal state (sharpened).
     */
    public function advance(): void
    {
        $next = $this->status->next();

        if ($next === null) {
            throw new \LogicException(
                "Audit #{$this->id} is already at terminal status [{$this->status->value}]"
            );
        }

        $timestamps = [
            AuditStatus::Analyzing->value => 'analyzing_started_at',
            AuditStatus::Diagnosed->value => 'diagnosed_at',
            AuditStatus::Sharpened->value => 'sharpened_at',
        ];

        $this->status = $next;

        if (isset($timestamps[$next->value])) {
            $this->{$timestamps[$next->value]} = now();
        }

        $this->save();
    }

    // ─── Query Scopes ─────────────────────────────────────────────────────

    /** Only demo audits (is_demo = true). */
    #[Scope]
    protected function demo(Builder $query): void
    {
        $query->where('is_demo', true);
    }

    /** Only audits not yet claimed by a registered user. */
    #[Scope]
    protected function unclaimed(Builder $query): void
    {
        $query->whereNull('claimed_at');
    }

    /**
     * Demo audits whose session has expired and eligible for cleanup.
     * Used by the scheduled CleanExpiredDemoAudits command.
     */
    #[Scope]
    protected function expiredDemos(Builder $query): void
    {
        $query->where('is_demo', true)
            ->whereNull('claimed_at')
            ->where('demo_session_expires', '<', now());
    }

    // ─── Relationships ────────────────────────────────────────────────────

    /** The workspace this audit belongs to. */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /** The user who created this audit. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The previous version of this audit in the version chain.
     * NULL for root versions (isRootVersion = true)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Audit::class, 'parent_audit_id');
    }

    /** All child versions created from this audit via newVersion() */
    public function versions(): HasMany
    {
        return $this->hasMany(Audit::class, 'parent_audit_id');
    }

    /** All findings produced by the rule engine for this audit. */
    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    // TODO: add further relations to (Findings, ForumThread, MCPLogs)
}
