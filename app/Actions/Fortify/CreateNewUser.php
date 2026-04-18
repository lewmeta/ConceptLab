<?php

namespace App\Actions\Fortify;

use App\Actions\ProvisionWorkspace;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(
        private readonly Request $request,
        private readonly ProvisionWorkspace $provision,
    ) {}

    /**
     * Validate input, create the user, provision a workspace, and
     * dispatch ClaimDemoAudit if a demo session cookie is present.
     *
     * Wrapped in a transaction — a user without a workspace is an
     * invalid state. If workspace creation fails, the user row is
     * rolled back and the registration is retried cleanly.
     *
     * ClaimDemoAudit dispatches afterCommit() so the job cannot run
     * ProvisionWorkspace — the single source of truth for that logic.
     */
    public function create(array $input): User
    {
        $this->validate($input);

        return DB::transaction(function () use ($input) {

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            $this->provision->execute($user, $this->request);

            return $user;
        });
    }

    /**
     * Validate the input
     */
    public function validate(array $input): void
    {
        validator($input, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:180', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()]
        ])->validate();
    }
}
