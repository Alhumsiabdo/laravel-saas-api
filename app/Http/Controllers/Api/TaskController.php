<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {}

    public function index(Project $project): AnonymousResourceCollection
    {
        Gate::authorize('view', $project);
        $tasks = $this->taskService->getAll($project);
        return TaskResource::collection($tasks);
    }

    public function store(CreateTaskRequest $request, Project $project): TaskResource
    {
        Gate::authorize('view', $project);
        $task = $this->taskService->create($project, $request->validated());
        return new TaskResource($task->load('assignee'));
    }

    public function show(Project $project, Task $task): TaskResource
    {
        Gate::authorize('view', $task);
        return new TaskResource($task->load('assignee'));
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): TaskResource
    {
        Gate::authorize('update', $task);
        $task = $this->taskService->update($task, $request->validated());
        return new TaskResource($task->load('assignee'));
    }

    public function destroy(Project $project, Task $task): JsonResponse
    {
        Gate::authorize('delete', $task);
        $this->taskService->delete($task);
        return response()->json(['message' => 'Task deleted']);
    }
}
