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

    protected function casts(): array
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
     * Create a unique workspace slug derived from an email's local-part.
     *
     * Builds the base from the substring before `@`, replaces `.`, `_`, `+`, `/`, and `\` with spaces, slugifies that base, and if it collides with an existing (including soft-deleted) workspace slug appends `-2`, `-3`, ... until a unique slug is found.
     *
     * @param string $email The email address to derive the slug from (uses the part before `@`).
     * @return string The generated unique slug.
     */
    public static function generateSlugFromEmail(string $email): string
    {

        // $base = Str::of($email)
        //     ->before('@')
        //     ->replace('.', ' ')
        //     ->slug()
        //     ->toString(); --- IGNORE ---

        $base = Str::of($email)
            ->before('@')
            ->replace(['.', '_', '+', '/', '\\'], ' ')
            ->slug()
            ->toString(); // Result: "lewis-nakitare"

        $slug = $base;
        $suffix = 2;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
