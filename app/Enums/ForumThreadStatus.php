<?php

namespace App\Enums;

enum ForumThreadStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            ForumThreadStatus::Open => 'Open',
            ForumThreadStatus::Resolved => 'Resolved',
            ForumThreadStatus::Closed => 'Closed',
        };
    }

    /** Closing a Forum thread triggers the audit → sharpened transition. */
    public function triggersSharpened(): bool
    {
        return $this === ForumThreadStatus::Closed;
    }

    public function isActive(): bool
    {
        return $this === ForumThreadStatus::Open;
    }
}
