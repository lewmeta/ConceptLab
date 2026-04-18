<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';

    /**
     * Get the label for the user role.
     */
    public function label(): string
    {
        return match ($this) {
            UserRole::Admin => 'Admin',
            UserRole::User => 'User',
        };
    }

    /** Check if the user is admin */
    public function isAdmin(): bool
    {
        return $this === UserRole::Admin;
    }
}
