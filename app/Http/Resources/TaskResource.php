<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'priority'    => $this->priority,
            'due_date'    => $this->due_date?->toDateString(),
            'assignee'    => $this->whenLoaded('assignee', fn() => [
                'id'   => $this->assignee->id,
                'name' => $this->assignee->name,
            ]),
            'project_id'  => $this->project_id,
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}
