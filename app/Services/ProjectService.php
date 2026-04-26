<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class ProjectService
{
    public function getAll(Workspace $workspace): Collection
    {
        return $workspace->projects()->withCount('tasks')->get();
    }

    public function create(Workspace $workspace, array $data): Project
    {
        return $workspace->projects()->create($data);
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);
        return $project;
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }
}
