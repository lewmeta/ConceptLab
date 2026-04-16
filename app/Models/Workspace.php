<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['owner_id', 'name', 'slug', 'domain_profile', 'created_from_demo'])]
class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    protected function cast(): array
    {
        return [
            'created_from_demo' => 'boolean',
        ];
    }

    // ─── PHP 8.4 Property Hooks ───────────────────────────────────────────

    /** 
     * Whether a domain profile has been provided
     * Gates AI repair context - repairs without a profile use generic templates
     */
    public bool $hasDomainProfile {
        get => filled($this->domain_profile);
    }

    // ─── Relationships ────────────────────────────────────────────────────

    /** 
     * The user who own and administers this workspace
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * All memberships records for this workspace (includes pending invitations)
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(WorkspaceMembership::class);
    }

    /**
     * All accepted members via the pivot table
     * Use memberships() when you need to inspect the roles or invitation state
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspace_memberships')
            ->withPivot(['role', 'invited_by', 'accepted_at'])
            ->withTimestamps();
    }

    // ─── Slug Generation ─────────────────────────────────────────────────

    /**
     * Generate a unique workspace slug from an email prefix.
     * Appends an incrementing numeric siffix on collission: username-2, username-3, etc.
     * Checks soft-deleted workspaces to prevent reuse of deleted slugs
     */
    public static function generateSlugFromEmail(string $email): string
    {
        $base = Str::slug(explode('@', $email)[0]);
        $slug = $base;
        $suffix = 2;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
