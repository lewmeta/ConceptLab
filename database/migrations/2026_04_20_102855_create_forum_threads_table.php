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
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('audit_id')
                ->constrained('audits')->cascadeOnDelete();

            // Denormalised from audit for efficient workspace-scoped queries
            // Avoids a join through audits on every workspace dashboard query
            $table->foreignId('workspace_id')
                ->constrained('workspaces')->cascadeOnDelete();

            // ForumThreadStatus enum: open | resolved | closed
            $table->string('status', 20)->default('open');

            // Cached counts — incremented on insert to forum_messages
            // V1 caps the interrogator at 3 questions (enforced in application)
            // No max constraint at DB level — V2 can raise the limit without migration
            $table->unsignedTinyInteger('question_count')->default(0);
            $table->unsignedTinyInteger('user_response_count')->default(0);

            // AI-generated summary of what the Forum revealed about definition gaps
            // Generated as a background job when status moves to resolved
            // AI-generated summary — NULL until thread resolves
            $table->text('resolution_summary')->nullable();

            $table->timestamps();

            // Enforces one Forum thread per audit in V1
            // Relaxed in V2 by removing this constraint and adding a round column
            $table->unique('audit_id');

            $table->index('workspace_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_threads');
    }
};
