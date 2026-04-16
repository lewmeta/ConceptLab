<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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


    // ─── OAuth Factory ────────────────────────────────────────────────────

    /**
     * Finds or creates a User corresponding to a Socialite OAuth account.
     *
     * Matches records by `provider` and `provider_id`. If a new user is created,
     * sets `name`, `email`, `avatar_url`, `email_verified_at` to the current time,
     * and `role` to `UserRole::User`.
     *
     * @param \Laravel\Socialite\Contracts\User $socialiteUser The Socialite user payload from the OAuth provider.
     * @param string $provider The provider identifier (e.g., "github", "google").
     * @return static The existing or newly created User instance.
     */
    public static function fromSocialite(SocialiteUser $socialiteUser, string $provider): static
    {
        return static::firstOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $socialiteUser->getId(),
            ],
            [
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'avatar_url' => $socialiteUser->getAvatar(),
                'email_verified_at' => now(),
                'role' => UserRole::User,
            ],
        );
    }
}
