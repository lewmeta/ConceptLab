<?php

namespace App\Actions;

use App\Enums\AuditStatus;
use App\Models\Audit;
use App\Services\LogicAuditor;
use Illuminate\Support\Str;

/**
 * Single-purpose action: accept validated text, create a frozen Demo
 * Audit row, run the LogicAuditor engine, return the diagnosed Audit.
 *
 * input_char_count and input_word_count are not set here.
 * Audit::booted() derives them from input_text automatically on create.
 *
 * The empty-string guard is defensive — the Livewire component validates
 * before calling execute(), so this path is not reachable in practice
 * via the normal flow. It exists to protect direct callers in tests.
 */
class CreateDemoAudit
{
    public function __construct(
        private readonly LogicAuditor $auditor
    ) {}

    public function execute(string $inputText): Audit
    {
        $text = trim($inputText);

        if ($text === '') {
            throw new \InvalidArgumentException('Input text must not be empty.');
        }

        $audit = Audit::create([
            'workspace_id' => null,
            'user_id' => null,
            'version' => 1,
            'input_text' => $text,
            'status' => AuditStatus::Draft,
            'is_demo' => true,
            'demo_session_key' => Str::random(64),
            'demo_session_expires' => now()->addHours(24),
        ]);

        return $this->auditor->run($audit);
    }
}
