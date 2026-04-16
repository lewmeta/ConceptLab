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
        Schema::create('workspace_memberships', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('user_id');

            // WorkspaceRole enum: owner > admin > member > viewer
            // V1 surfaces only owner — other roles are data-ready for V2
            // Cast to App\Enums\WorkspaceRole on the model
            $table->string('role')->default('member');

            // Null for the workspace owner  (self-invited on creation)
            $table->unsignedBigInteger('invited_by')->nullable();

            // NULL = invitation pending acceptance
            // NOT NULL = active member
            $table->timestamp('accepted_at')->nullable();

            // One membership record per user per workspace
            $table->unique(['workspace_id', 'user_id']);

            // Indexing
            $table->index('user_id');
            $table->index('workspace_id');
            $table->index('role');

            // Referencial integrity contraints
            $table->foreign('workspace_id')
                ->references('id')
                ->on('workspaces')
                ->cascadeOnDelete();

            $table->foreign('invited_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_memberships');
    }
};
