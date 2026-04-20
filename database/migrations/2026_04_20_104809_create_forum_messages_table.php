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
        Schema::create('forum_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('thread_id')
                ->constrained('forum_threads')->cascadeOnDelete();

            // interrogator = AI-generated question
            // user         = human response
            // ForumMessageRole enum: interrogator | user
            $table->string('role', 20);

            $table->text('content');

            // Which finding prompted this interrogator question
            // NULL for user replies and for opening questions that
            // reference the whole audit rather than a specific finding
            $table->foreignId('referenced_finding_id')
                ->nullable()
                ->constrained('findings')->nullOnDelete();

            // Turn order within the thread — authoritative ordering column
            // Do not rely on created_at for ordering in high-load scenarios
            $table->unsignedInteger('sequence');

            // TRUE for the first question in the thread
            // Generated from the most critical open finding
            $table->boolean('is_opening_question')->default(false);

            // Logged for debugging and cost attribution
            // NULL for user messages
            $table->string('ai_model_used', 50)->nullable();

            // Approximate tokens used for this message
            // NULL for user messages
            $table->unsignedSmallInteger('token_count')->nullable();

            // No updated_at — messages are immutable
            // The Forum is a permanent record of the dialogue
            $table->timestamp('created_at');

            $table->index('thread_id');
            $table->index('referenced_finding_id');
            $table->index('role');

            // Chronological thread rendering
            $table->index(['thread_id', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_messages');
    }
};
