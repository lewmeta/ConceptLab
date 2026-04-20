<?php

namespace App\Enums;

enum FindingStatus: string
{
    case Open         = 'open';
    case Acknowledged = 'acknowledged';
    case Repaired     = 'repaired';
    case Dismissed    = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            FindingStatus::Open         => 'Open',
            FindingStatus::Acknowledged => 'Acknowledged',
            FindingStatus::Repaired     => 'Repaired',
            FindingStatus::Dismissed    => 'Dismissed',
        };
    }

    /**
     * Whether a finding in this status can still have a repair requested.
     * Only open and acknowledged findings are eligible.
     */
    public function canRequestRepair(): bool
    {
        return match ($this) {
            FindingStatus::Open,
            FindingStatus::Acknowledged => true,
            FindingStatus::Repaired,
            FindingStatus::Dismissed => false,
        };
    }

    /** Whether this is a terminal status (no further transitions possible) */
    public function isTerminal(): bool
    {
        return match ($this) {
            FindingStatus::Repaired,
            FindingStatus::Dismissed => true,
            FindingStatus::Open,
            FindingStatus::Acknowledged => false,
        };
    }

    public function isResolved(): bool
    {
        return $this === FindingStatus::Repaired
            || $this === FindingStatus::Dismissed;
    }
}
