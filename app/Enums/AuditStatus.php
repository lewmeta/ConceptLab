<?php

namespace App\Enums;

enum AuditStatus: string
{
    case Draft = 'draft';
    case Analyzing = 'analyzing';
    case Diagnosed = 'diagnosed';
    case Sharpened = 'sharpened';

    /**
     * Returns the next valid status in the forward-only state machine.
     * The model layer must reject any transition that does not follow this chain.
     */
    public function next(): ?AuditStatus
    {
        return match ($this) {
            AuditStatus::Draft => AuditStatus::Analyzing,
            AuditStatus::Analyzing => AuditStatus::Diagnosed,
            AuditStatus::Diagnosed => AuditStatus::Sharpened,
            AuditStatus::Sharpened => null, // Terminal state
        };
    }

    /**
     * Checks if transitioning from the current status to the target status is valid according to the state machine rules.
     */
    public function canTransitionTo(AuditStatus $target): bool
    {
        return $this->next() === $target;
    }

    /**
     * Get the label for the audit status.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            AuditStatus::Draft => 'Draft',
            AuditStatus::Analyzing => 'Analyzing',
            AuditStatus::Diagnosed => 'Diagnosed',
            AuditStatus::Sharpened => 'Sharpened',
        };
    }
}
