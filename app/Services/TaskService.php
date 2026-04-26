<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Collection;

class TaskService
{
    public function getAll(Project $project): Collection
    {
        return $project->tasks()->with('assignee')->get();
    }

    public function create(Project $project, array $data): Task
    {
        return $project->tasks()->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

}
