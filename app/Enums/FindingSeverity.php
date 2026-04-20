<?php

namespace App\Enums;

enum FindingSeverity: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Critical = 'critical';

    /**
     * Maps severity_weight from the heuristics table to a FindingSeverity.
     * The finding's severity is derived from the highest weight among
     * all heuristics that fired on its span.
     */
    public static function fromWeight(int $weight): self
    {
        return match (true) {
            $weight >= 3 => FindingSeverity::Critical,
            $weight === 2 => FindingSeverity::Warning,
            default => FindingSeverity::Info,
        };
    }

    public function label(): string
    {
        return match ($this) {
            FindingSeverity::Info => 'Informational',
            FindingSeverity::Warning => 'Warning',
            FindingSeverity::Critical => 'Critical'
        };
    }

    /**
     * Numeric sort weight — higher = more severe.
     * Used when ordering findings in the Socratic Sidebar.
     */
    public function sortWeight(): int
    {
        return match ($this) {
            FindingSeverity::Critical => 3,
            FindingSeverity::Warning => 2,
            FindingSeverity::Info => 1,
        };
    }
}
