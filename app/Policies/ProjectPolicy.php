<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $project->workspace->members()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function update(User $user, Project $project): bool
    {
        return $project->workspace->members()
            ->where('user_id', $user->id)
            ->whereIn('pivot_role', ['owner', 'admin'])
            ->exists();
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->workspace->owner_id === $user->id;
    }
}
