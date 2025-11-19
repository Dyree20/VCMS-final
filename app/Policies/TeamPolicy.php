<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Only admins can create teams
     */
    public function create(User $user): bool
    {
        return $user->role_id === 1; // 1 = Admin
    }

    /**
     * Only admins can view teams
     */
    public function view(User $user, Team $team): bool
    {
        return $user->role_id === 1;
    }

    /**
     * Only admins can update teams
     */
    public function update(User $user, Team $team): bool
    {
        return $user->role_id === 1;
    }

    /**
     * Only admins can delete teams
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->role_id === 1;
    }
}
