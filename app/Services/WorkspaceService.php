<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WorkspaceService
{
    public function getAll(User $user): Collection
    {
        return $user->workspaces()->with('owner')->get();
    }

    public function create(User $user, array $data): Workspace
    {
        $workspace = Workspace::create([
            'name'     => $data['name'],
            'slug'     => Str::slug($data['name']) . '-' . uniqid(),
            'owner_id' => $user->id,
        ]);

        $workspace->members()->attach($user->id, ['role' => 'owner']);

        return $workspace;
    }

    public function update(Workspace $workspace, array $data): Workspace
    {
        $workspace->update($data);
        return $workspace;
    }

    public function delete(Workspace $workspace): void
    {
        $workspace->delete();
    }
}
