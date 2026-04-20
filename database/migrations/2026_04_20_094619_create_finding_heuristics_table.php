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
        Schema::create('finding_heuristics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('finding_id')->constrained('findings')->cascadeOnDelete();
            $table->foreignId('heuristic_id')->constrained('heuristics')->restrictOnDelete(); // Heuristic can't be deleted.

            // false = rule engine triggered | true = Job A (AI safety net)
            $table->boolean('triggered_by_ai')->default(false);

            // Confidence score for AI-triggered rows only
            // NULL for rule-based triggers (rule fires or it does not)
            $table->decimal('trigger_score', 4, 2)->nullable();

            // Sub-span within the finding that fired this rule
            // May be narrower than findings.excerpt
            $table->string('trigger_excerpt', 400)->nullable();

            // Immutable log row — no updated_at
            $table->timestamp('created_at');

            // A rule fires at most once per finding
            $table->unique(['finding_id', 'heuristic_id']);

            $table->index('heuristic_id');
            $table->index('triggered_by_ai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finding_heuristics');
    }
};
