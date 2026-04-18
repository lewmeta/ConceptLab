<?php

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\WorkspaceMembership;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);
// uses(LazilyRefreshDatabase::class);

// ── Fortify registration ──────────────────────────────────────────────────────

test('Registration creates a user row', function () {
    Queue::fake();

    $this->post('/register', [
        'name' => 'Lewis Test',
        'email' => 'lewis@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect();

    expect(User::where('email', 'lewis@example.com')->exists())->toBeTrue();
});

test('Registration creates a workspace and sets current_workspace_id', function () {
    Queue::fake();

    $this->post('/register', [
        'name' => 'Lewis Test',
        'email' => 'lewis@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'lewis@example.com')->first();

    expect($user->current_workspace_id)->not()->toBeNull();
    $this->assertModelExists($user->currentWorkspace);
});

test('registration creates an owner membership', function () {
    Queue::fake();

    $this->post('/register', [
        'name'                  => 'Lewis Nakitare',
        'email'                 => 'lewis@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $user = User::where('email', 'lewis@example.com')->firstOrFail();
    $membership = WorkspaceMembership::where('user_id', $user->id)->firstOrFail();

    expect($membership->role)->toBe(WorkspaceRole::Owner)
        ->and($membership->hasAccepted)->toBeTrue();
});

it('registration generates workspace slug from email prefix', function () {
    Queue::fake();

    $this->post('/register', [
        'name'                  => 'Lewis Nakitare',
        'email'                 => 'lewis.nakitare@gmail.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $user = User::where('email', 'lewis.nakitare@gmail.com')->firstOrFail();

    $user->refresh();

    expect($user->currentWorkspace->slug)->toStartWith('lewis-nakitare');
});


//TODO: Add test for ClaimAudit assertion


// ── Registration validation ───────────────────────────────────────────────────
it('registration rejects missing name', function () {
    $this->post('/register', [
        'email' => 'lewis@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('name');
});

it('registration rejects duplicate email', function () {
    Queue::fake();
    User::factory()->create(['email' => 'lewis.nikitare@example.com']);

    $this->post('/register', [
        'name' => 'Lewis Nikitare',
        'email' => 'lewis.nikitare@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');
});

it('registration rejects mismatched passwords', function () {
    $this->post('/register', [
        'name' => 'Lewis Test',
        'email' => 'lewis@example.com',
        'password' => 'password',
        'password_confirmation' => 'Different1'
    ])->assertSessionHasErrors('password');
});

// ── Login ─────────────────────────────────────────────────────────────────────

it('Login authenticates an existing user', function () {
    $user = User::factory()->create([
        'email' => 'lewis@example.com',
        'password' => bcrypt('password'),
    ]);

    $this->post('/login', [
        'email' => 'lewis@example.com',
        'password' => 'password',
    ])->assertRedirect();
});

it('login rejects incorrect password', function () {
    User::factory()->create([
        'email'    => 'lewis@example.com',
        'password' => bcrypt('Password1!'),
    ]);

    $this->post('/login', [
        'email'    => 'lewis@example.com',
        'password' => 'WrongPassword!',
    ])->assertSessionHasErrors();

    $this->assertGuest();
});

// ── OAuth email collision scenarios ──────────────────────────────────────────

it('Scenario A - returning OAuth user is found by provider and provider_id', function () {
    $user = User::factory()->create([
        'email' => 'lewis@example.com',
        'provider' => 'google',
        'provider_id' => 'google-uuid-123',
    ]);

    $result = User::fromSocialite(
        mockSocialiteUser('google-uuid-123', 'lewis@example.com'),
        'google',
    );

    expect($result->id)->toBe($user->id)
        ->and(User::count())->toBe(1); // No new user created
});

it('Scenario B — email collision links OAuth credentials to existing account', function () {
    // User registered with email/password — no provider columns set
    $existing = User::factory()->create([
        'email'       => 'lewis@example.com',
        'provider'    => null,
        'provider_id' => null,
    ]);

    $result = User::fromSocialite(
        mockSocialiteUser('google-uid-123', 'lewis@example.com'),
        'google'
    );

    $existing->refresh();

    // Must return the existing user, not create a new one
    expect($result->id)->toBe($existing->id)
        ->and(User::count())->toBe(1)
        ->and($existing->provider)->toBe('google')
        ->and($existing->provider_id)->toBe('google-uid-123');
});

it('Scenario C — brand new OAuth user is created', function () {
    expect(User::count())->toBe(0);
 
    $result = User::fromSocialite(
        mockSocialiteUser('google-uid-456', 'newuser@example.com'),
        'google'
    );
 
    expect(User::count())->toBe(1)
        ->and($result->email)->toBe('newuser@example.com')
        ->and($result->provider)->toBe('google')
        ->and($result->provider_id)->toBe('google-uid-456')
        ->and($result->wasRecentlyCreated)->toBeTrue();
});
 
it('Scenario C — new OAuth user email is pre-verified', function () {
    $result = User::fromSocialite(
        mockSocialiteUser('google-uid-789', 'brand.new@example.com'),
        'google'
    );
 
    expect($result->email_verified_at)->not->toBeNull();
});

/**
 * Creates a minimal Socialite user mock for testing formSocialite() scenarios.
 * Uses an anonymous class to avoid a Mockery dependency in Module 0 tests.
 */
function mockSocialiteUser(string $id, string $email, ?string $avatar = null): \Laravel\Socialite\Contracts\User
{
    return new class($id, $email, $avatar) implements \Laravel\Socialite\Contracts\User {
        public function __construct(
            private string $id,
            private string $email,
            private ?string $avatar,
        ) {}

        public function getId(): string
        {
            return $this->id;
        }
        public function getNickname(): ?string
        {
            return null;
        }
        public function getName(): ?string
        {
            return 'Test User';
        }
        public function getEmail(): string
        {
            return $this->email;
        }
        public function getAvatar(): ?string
        {
            return $this->avatar;
        }
        public function getRaw(): array
        {
            return [];
        }
        public function setRaw(array $user): static
        {
            return $this;
        }
        public function map(array $attributes): static
        {
            return $this;
        }
        public function offsetExists(mixed $offset): bool
        {
            return false;
        }
        public function offsetGet(mixed $offset): mixed
        {
            return null;
        }
        public function offsetSet(mixed $offset, mixed $value): void {}
        public function offsetUnset(mixed $offset): void {}
        public function token(): string
        {
            return 'fake-token';
        }
        public function refreshToken(): ?string
        {
            return null;
        }
        public function expiresIn(): ?int
        {
            return null;
        }
        public function approvedScopes(): array
        {
            return [];
        }
    };
}
