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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();

            // Nullable - demo audits have no workspace or until claimed
            // Both are populated by ClaimDemoAudit after the user registers.
            // foreignId() is correct here: parent uses $table->id() and column
            // names follow the singular_table_id convention.
            $table->foreignId('workspace_id')->nullable()
                ->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()
                ->constrained()->restrictOnDelete();

            // Seft-referential version chain - parent_audit-id does not follow
            // the singular_table_id convention so foreignId() would be clunky.
            // Explicit declaration is clearer here
            $table->unsignedBigInteger('parent_audit_id')
                ->nullable();

            // Version number within the chain — first version = 1
            $table->unsignedBigInteger('version')->default(1);

            // Optional user-given name.
            // Auto-generated if NULL: "Audit · {date} · v{N}"
            $table->string('title', 200)->nullable();

            // FROZEN. Written once at INSERT. Never updated.
            // All findings.start_char / end_char reference this text.
            // mediumText supports up to 16MB — safe for any realistic document size.
            $table->mediumText('input_text');

            // Derived from input_text at creation — see Audit::booted().
            // unsignedMediumInteger supports up to 16.7M — safe for char/word counts.
            // findings.end_char must be <= input_char_count.
            $table->unsignedMediumInteger('input_char_count');
            $table->unsignedMediumInteger('input_word_count');

            // Forward-only state machine: draft → analyzing → diagnosed → sharpened
            // Backward transitions are rejected at the model layer.
            $table->string('status', 20)->default('draft');

            // 0.00 to 100.00 — NULL until status = diagnosed
            $table->decimal('sharpness_score', 5, 2)->nullable();

            // Demo session management
            $table->boolean('is_demo')->default(false);

            // Short-lived token matching the browser cookie set during demo.
            // Used by ClaimDemoAudit job to assign user_id and workspace_id.
            $table->string('demo_session_key', 64)->nullable();

            // Expiry for unclaimed demo audits.
            // Cleanup job soft-deletes unclaimed rows past this timestamp.
            $table->timestamp('demo_session_expires')->nullable();

            // Set by ClaimDemoAudit job on successful claim.
            // NULL = unclaimed demo | NOT NULL = permanently owned by a user.
            $table->timestamp('claimed_at')->nullable();

            // Job A (AI safety net) completion flag
            $table->boolean('ai_safety_net_completed')->default(false);

            // Lifecycle timestamps — one per state transition, NULL until entered
            $table->timestamp('analyzing_started_at')->nullable();
            $table->timestamp('diagnosed_at')->nullable();
            $table->timestamp('sharpened_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ── Indexes ───────────────────────────────────────────────────

            $table->index('demo_session_key');
            $table->index('status');

            // Composite indexes for the most common query patterns
            $table->index(['workspace_id', 'status']);
            $table->index(['workspace_id', 'created_at']);

            // Unique constraint on version chain — prevents duplicate versions.
            // The unique constraint creates an implicit index on these columns,
            // so no separate index() call is needed for the same pair.
            $table->unique(['parent_audit_id', 'version']);

            // ── Foreign keys ──────────────────────────────────────────────

            // Self-referential FK — nullOnDelete so orphaned versions do not
            // cascade-delete the entire chain when a parent is soft-deleted.
            $table->foreign('parent_audit_id')
                ->references('id')
                ->on('audits')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
