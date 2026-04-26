<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\CreateProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\Workspace;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService
    ) {}

    public function index(Workspace $workspace): AnonymousResourceCollection
    {
        $this->authorize('view', $workspace);
        $projects = $this->projectService->getAll($workspace);
        return ProjectResource::collection($projects);
    }

    public function store(CreateProjectRequest $request, Workspace $workspace): ProjectResource
    {
        $this->authorize('view', $workspace);
        $project = $this->projectService->create($workspace, $request->validated());
        return new ProjectResource($project);
    }

    public function show(Workspace $workspace, Project $project): ProjectResource
    {
        $this->authorize('view', $project);
        return new ProjectResource($project->load('workspace'));
    }

    public function update(UpdateProjectRequest $request, Workspace $workspace, Project $project): ProjectResource
    {
        $this->authorize('update', $project);
        $project = $this->projectService->update($project, $request->validated());
        return new ProjectResource($project);
    }

    public function destroy(Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('delete', $project);
        $this->projectService->delete($project);
        return response()->json(['message' => 'Project deleted']);
    }
}
