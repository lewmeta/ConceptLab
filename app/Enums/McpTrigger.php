<?php

namespace App\Enums;

enum McpTrigger: string
{
    case System = 'system'; // Automatic pipeline step
    case User   = 'user';   // User action (e.g. repair click)
    case Forum  = 'forum';  // Forum Lite question generation

    public function label(): string
    {
        return match ($this) {
            McpTrigger::System => 'System',
            McpTrigger::User   => 'User',
            McpTrigger::Forum  => 'Forum',
        };
    }
}
