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
        Schema::create('findings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('audit_id')->constrained('audits')->cascadeOnDelete();

            // Character offsets into audits.input_text (frozen)
            // Validated at application layer:
            //   end_char > start_char
            //   end_char <= audits.input_char_count
            $table->unsignedSmallInteger('start_char');
            $table->unsignedSmallInteger('end_char');

            // Snapshot of highlighted text stored at creation — never updated
            $table->string('excerpt', 400);

            // ConfusionType enum — determines highlight colour in UI
            $table->string('primary_confusion_type', 40);

            // FindingSeverity enum: info | warning | critical
            $table->string('severity', 20);

            // FindingStatus enum: open → acknowledged → repaired | dismissed
            $table->string('status', 20)->default('open');

            // AI-generated repair — NULL until user clicks repair button
            $table->text('repair_text')->nullable();
            $table->timestamp('repair_requested_at')->nullable();
            $table->timestamp('repair_generated_at')->nullable();

            $table->timestamp('dismissed_at')->nullable();
            $table->string('dismissed_reason', 200)->nullable();

            $table->timestamps();

            $table->index('audit_id');
            $table->index('status');
            $table->index('primary_confusion_type');
            $table->index(['audit_id', 'status']);
            $table->index(['audit_id', 'severity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
