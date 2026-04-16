<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMembership;
use App\WorkspaceRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(
        private readonly Request $request
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
     * before the user and workspace rows are visible to the queue worker.
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

            $workspace = Workspace::create([
                'owner_id' => $user->id,
                'name' => "{$user->displayName}'s Workspace",
                'slug' => Workspace::generateSlugFromEmail($input['email']),
                'created_from_demo' => false,
            ]);

            WorkspaceMembership::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'role' => WorkspaceRole::Owner,
                'accepted_at' => now(),
            ]);

            $user->update(['current_workspace_id' => $workspace->id]);

            // TODO: Dispatch demo claim after commit - the job reads the session
            // from the cookie set by the LogicInput Livewire component.

            //TODO: Add Claim audit job dispatch.

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

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    // public function create(array $input): User
    // {
    //     Validator::make($input, [
    //         ...$this->profileRules(),
    //         'password' => $this->passwordRules(),
    //     ])->validate();

    //     return User::create([
    //         'name' => $input['name'],
    //         'email' => $input['email'],
    //         'password' => $input['password'],
    //     ]);
    // }
}
