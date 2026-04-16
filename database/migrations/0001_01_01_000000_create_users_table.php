<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);

            // Nullable — OAuth users from some providers may not expose email
            $table->string('email', 180)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();

            // Nullable — OAuth users authenticate via provider, not password
            $table->string('password')->nullable();

            // Cast to App\Enums\UserRole on the model
            $table->string('role', 20)->default('user');
            $table->string('avatar_url', 500)->nullable();

            // Socialite OAuth columns
            // NULL for email/password users — see unique constraint note below
            $table->string('provider', 30)->nullable();
            $table->string('provider_id', 100)->nullable();

            // FK added in workspaces migration to avoid circular dependency
            // Nullable at registration — workspace is created in the same request
            $table->unsignedBigInteger('current_workspace_id')->nullable();

            // NULL = onboarding sequence not yet completed
            $table->timestamp('onboaring_completed_at')->nullable();


            $table->rememberToken();
            $table->timestamps();

            // Soft delete — row is anonymised on deletion, not physically destroyed
            // Satisfies GDPR right-to-erasure without breaking FK references 
            $table->softDeletes();

            // Indexing
            $table->index('current_workspace_id');
            $table->index('role');
            $table->index('deleted_at');

            // NULL/NULL rows are permitted — email/password users have no provider
            // Uniqueness is only enforced when both columns are non-NULL
            $table->unique(['provider', 'provider_id']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
