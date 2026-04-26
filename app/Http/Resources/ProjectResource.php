<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'status'      => $this->status,
            'tasks_count' => $this->tasks_count ?? 0,
            'workspace'   => [
                'id'   => $this->workspace_id,
                'name' => $this->whenLoaded('workspace', fn() => $this->workspace->name),
            ],
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}
