<?php

namespace App\Enums;

enum ForumMessageRole: string
{
    case Interrogator = 'interrogator';
    case User         = 'user';

    public function label(): string
    {
        return match ($this) {
            ForumMessageRole::Interrogator => 'Forum',
            ForumMessageRole::User         => 'You',
        };
    }

    public function isAi(): bool
    {
        return $this === ForumMessageRole::Interrogator;
    }
}
