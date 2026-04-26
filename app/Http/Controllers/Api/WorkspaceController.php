<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\CreateWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorkspaceController extends Controller
{
    public function __construct(
        private readonly WorkspaceService $workspaceService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $workspaces = $this->workspaceService->getAll(auth()->user());
        return WorkspaceResource::collection($workspaces);
    }

    public function store(CreateWorkspaceRequest $request): WorkspaceResource
    {
        $workspace = $this->workspaceService->create(
            auth()->user(),
            $request->validated()
        );

        return new WorkspaceResource($workspace);
    }

    public function show(Workspace $workspace): WorkspaceResource
    {
        $this->authorize('view', $workspace);
        return new WorkspaceResource($workspace->load('owner'));
    }

    public function update(UpdateWorkspaceRequest $request, Workspace $workspace): WorkspaceResource
    {
        $this->authorize('update', $workspace);
        $workspace = $this->workspaceService->update($workspace, $request->validated());
        return new WorkspaceResource($workspace);
    }

    public function destroy(Workspace $workspace): JsonResponse
    {
        $this->authorize('delete', $workspace);
        $this->workspaceService->delete($workspace);
        return response()->json(['message' => 'Workspace deleted'], 200);
    }
}
