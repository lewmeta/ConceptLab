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
        Schema::create('audit_mcp_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('audit_id')
                ->constrained('audits')->cascadeOnDelete();

             // e.g. "analyze_circularity", "detect_reification",
            //      "sync_figma_blueprint"
            $table->string('tool_name', 100);

            // What initiated this tool call
            // McpTrigger enum: system | user | forum
            $table->string('triggered_by', 20);

            // Stored for debugging and reproducibility
            // GDPR note: may contain user-authored text
            // Treat as internal-only in data exports
            $table->json('input_payload')->nullable();
            
             // Truncated at 10,000 chars at application layer
            $table->json('output_payload')->nullable();

            $table->boolean('succeeded');

            // NULL when succeeded = true
            $table->text('error_message')->nullable();
            
             // Wall-clock execution time in milliseconds
            // smallint unsigned = max 65,535ms (~65 seconds)
            // If a tool saturates this value, something else is wrong
            $table->unsignedSmallInteger('duration_ms')->nullable();

            // No updated_at — log rows are immutable
            $table->timestamp('created_at');

            $table->index('audit_id');
            $table->index('tool_name');
            $table->index('succeeded');

            $table->index(['audit_id', 'tool_name']);
            $table->index(['tool_name', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_mcp_logs');
    }
};
