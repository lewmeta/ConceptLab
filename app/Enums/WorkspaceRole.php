<?php

namespace App\Enums;

enum WorkspaceRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            WorkspaceRole::Owner => 'Owner',
            WorkspaceRole::Admin => 'Admin',
            WorkspaceRole::Viewer => 'Viewer',
            WorkspaceRole::Member => 'Member',
        };
    }

    /** Whether this role can perform write actions within a workspace */
    public function canWrite(): bool
    {
        return match ($this) {
            WorkspaceRole::Owner,
            WorkspaceRole::Admin,
            WorkspaceRole::Member => true,
            default => false,
        };
    }

    /**
     * Determines whether the role can manage workspace settings and members.
     *
     * @return bool `true` if the role can manage workspace settings and members, `false` otherwise.
     */
    public function canManage(): bool
    {
        return match ($this) {
            WorkspaceRole::Owner,
            WorkspaceRole::Admin => true,
            WorkspaceRole::Member,
            WorkspaceRole::Viewer => false
        };
    }
}
