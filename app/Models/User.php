<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Socialite\Contracts\User as SocialiteUser;

#[Fillable(['name', 'email', 'password', 'role', 'avatar_url', 'provider', 'provider_id', 'current_workspace_id', 'onboarding_completed_at', 'current_team_id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // ─── PHP 8.4 Property Hooks ───────────────────────────────────────────

    /** Display-safe name falling back through available identifiers */
    public string $displayName {
        get => $this->name
            ?? explode('@', $this->email ?? '')[0]
            ?? 'Anonymous';
    }

    /** Whether the user has completed the post-registration onboarding */
    public bool $hasCompletedOnboarding {
        get => $this->onboarding_completed_at !== null;
    }

    /** Whether the user holds the system administrator role */
    public bool $isAdmin {
        get => $this->role === UserRole::Admin;
    }

    // ─── Relationships ────────────────────────────────────────────────────

    /**
     * Get the workspace the user is currently active in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo BelongsTo relation to the Workspace model.
     */
    public function currentWorkspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'current_workspace_id');
    }

    /**
     * Get all workspace membership records for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany The user's WorkspaceMembership records across all workspaces.
     */
    public function workspaceMemberships(): HasMany
    {
        return $this->hasMany(WorkspaceMembership::class);
    }


    // ─── OAuth Factory ────────────────────────────────────────────────────

    /**
     * Find or create a User from a Socialite OAuth callback and link OAuth credentials when appropriate.
     *
     * Performs one of three outcomes: returns an existing user matching provider+provider_id; links the OAuth
     * credentials to an existing user found by email (if present) and returns that user; or creates and returns
     * a new user populated from the SocialiteUser.
     *
     * @param SocialiteUser $socialiteUser The Socialite user payload from the OAuth provider.
     * @param string $provider The OAuth provider identifier (e.g., "github", "google").
     * @return static The matching or newly created User model instance.
     */
    public static function fromSocialite(SocialiteUser $socialiteUser, string $provider): static
    {

        // Wrapped in a transaction to ensure atomicity of the find-or-create logic.
        return DB::transaction(function () use ($socialiteUser, $provider) {
            // Scenario A - returning OAuth user
            $existing = static::where('provider', $provider)
                ->where('provider_id', $socialiteUser->getId())
                ->first();

            if ($existing) {
                return $existing;
            }

            // Scenario B - email collision: link Oauth to existing account
            if ($socialiteUser->getEmail()) {
                $byEmail = static::where('email', $socialiteUser
                    ->getEmail())
                    ->first();

                if ($byEmail) {
                    $byEmail->update([
                        'provider' => $provider,
                        'provider_id' => $socialiteUser->getId(),
                        'avatar_url' => $socialiteUser->getAvatar() ?? $byEmail->avatar_url,
                        'email_verified_at' => $byEmail->email_verified_at ?? now(),
                    ]);

                    return $byEmail;
                }
            }

            // Scenario C - brand new user
            // Uses ForceCreate to bypass mass assignment protection (email_verified_at).
            return static::forceCreate(
                [
                    // 'name' => $socialiteUser->getName(),
                    'name' => $socialiteUser->getName()
                        ?? $socialiteUser->getNickname()
                        ?? explode('@', $socialiteUser->getEmail() ?? '')[0]
                        ?? 'User',

                    'email' => $socialiteUser->getEmail(),
                    'avatar_url' => $socialiteUser->getAvatar(),
                    'provider' => $provider,
                    'provider_id' => $socialiteUser->getId(),
                    'email_verified_at' => now(),
                    'role' => UserRole::User,
                ],
            );
        });
    }
}
