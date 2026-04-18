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

    /** The workspace the users is currently active in - the tenancy achor */
    public function currentWorkspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'current_workspace_id');
    }

    /** All workspace membership records across every workspace this user belongs to. */
    public function workspaceMemberships(): HasMany
    {
        return $this->hasMany(WorkspaceMembership::class);
    }


    // ─── OAuth Factory ────────────────────────────────────────────────────

    /**
     * Find or create a User from a Socialite OAuth callback.
     *
     * Handles three distinct scenarios:
     *
     * Scenario A — Returning OAuth user:
     *   provider + provider_id match → return existing user as-is.
     *
     * Scenario B — Email collision (critical):
     *   A user previously registered with email/password using the same
     *   email address. Their row has provider = null, provider_id = null.
     *   We link the OAuth credentials to their existing account rather
     *   than attempting to create a duplicate row (which would throw a
     *   unique constraint violation on the email column).
     *   The user gets OAuth login linked to their existing account.
     *
     * Scenario C — Brand new user:
     *   No matching row by provider+id or email → create a fresh user.
     *
     * The caller (SocialiteController) checks $user->wasRecentlyCreated
     * to determine whether ProvisionWorkspace should run. For Scenario B,
     * wasRecentlyCreated is false — the user already has a workspace and
     * ProvisionWorkspace's idempotency guard handles it regardless.
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
