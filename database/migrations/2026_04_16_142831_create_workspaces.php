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
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();

            // Owner always exists — FK added after users table exists
            $table->unsignedBigInteger('owner_id');

            // Or do the short hand refrencing via foreignId
            // $table->foreignId('owner_id')
            // ->constrained('users')
            // ->restrictOnDelete();

            $table->string('name', 100);

            // Auto-generated from email prefix at registration
            // e.g. lewis.nakitare@gmail.com → lewis-nakitare
            // Collisions get numeric suffix: lewis-nakitare-2
            $table->string('slug', 100)->unique();

            // Free-text domain context fed to AI repair generator
            // NULL until user completes first-repair modal
            $table->text('domain_profile')->nullable();

            // true when workspace was born from a demo claim
            $table->boolean('created_from_demo')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index('owner_id');
            $table->index('slug');
            $table->index('deleted_at');

            // Foreign key contraints - no cascade on delete to preserve workspace history and repairs
            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });

        // Now that workspaces exists, add the FK from users.current_workspace_id
        // This is the correct resolution for the circular dependency between
        // users and workspaces — FK added in a second Schema::table call
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('current_workspace_id')
                ->references('id')
                ->on('workspaces')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
