<?php

namespace App\Models;

use App\Enums\McpTrigger;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['audit_id', 'tool_name', 'triggered_by', 'input_payload', 'output_payload', 'succeeded', 'error_message', 'duration_ms',])]
class AuditMcpLog extends Model
{
    /** 
     * Append-only MCP execution log.
     * Records every tool call made during an audit lifecycle for debugging
     * and cost attribution. Never updated after insertion.
     *
     * GDPR note: input_payload may contain user-authored text.
     * Treat as internal-only in any data export pipeline.
     */
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'triggered_by'  => McpTrigger::class,
            'input_payload' => 'array',
            'output_payload' => 'array',
            'succeeded'     => 'boolean',
            'duration_ms'   => 'integer',
        ];
    }

    // ─── PHP 8.4 Property Hooks ───────────────────────────────────────────

    /** Whether this tool call failed (inverse of succeeded for readable conditionals). */
    public bool $failed {
        get => ! $this->succeeded;
    }

    // ─── Relationships ────────────────────────────────────────────────────

    /** The audit whose lifecycle this log entry belongs to. */
    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }
}
