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
        Schema::create('heuristics', function (Blueprint $table) {
            $table->id();

            // Canonical rule identifier: 1 through 60 for seed data
            // Admin-added rules continue from 61 upward
            $table->unsignedBigInteger('rule_number')->unique();

            // ConfusionType enum — 6 values, Option A (single assignment per rule)
            // Cast to App\Enums\ConfusionType on the model
            $table->string('confusion_type', 40);

            // Technical SBA name — internal, developer-facing
            $table->string('error_name', 100);

            // User-facing label — no jargon, no rule numbers
            $table->string('plain_name', 150);

            // JSON pattern detection configuration
            // Parsed by App\Services\LogicAuditor
            $table->json('trigger_logic');

            // Generic repair instruction used when AI repair is unavailable
            $table->text('repair_template');

            // Seed question for Forum Lite Clarification Interrogator
            // NULL = this rule does not generate Forum questions
            $table->text('forum_question_template')->nullable();

            // Contribution weight to sharpness score
            // 1 = informational | 2 = warning | 3 = critical
            $table->unsignedTinyInteger('severity_weight')->default(1);

            // Inactive rules are loaded but skipped by the engine
            // Allows disabling a rule without a deployment
            $table->boolean('is_active')->default(true);

            // Append-only versioning
            // When a rule needs correction: deactivate old, create new with supersedes_id
            // NULL = original rule (no predecessor)
            $table->unsignedBigInteger('supersedes_id')->nullable();

            // NULL = draft (not visible to engine)
            // NOT NULL = published and eligible to fire
            // Allows admin to stage new rules before making them live
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            // No softDeletes — heuristics are deactivated, never deleted
            // Deleting would corrupt finding_heuristics rows in historical audits

            $table->index('confusion_type');
            $table->index('is_active');
            $table->index('published_at');
            $table->index('supersedes_id');
            $table->index(['confusion_type', 'is_active']);
            $table->index(['is_active', 'published_at']); // Engine query index

            // Self-referential FK for rule evolution chain
            $table->foreign('supersedes_id')
                ->references('id')
                ->on('heuristics')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heuristics');
    }
};
