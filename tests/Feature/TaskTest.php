<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private function createSetup(): array
    {
        $user      = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $workspace->members()->attach($user->id, ['role' => 'owner']);
        $project   = Project::factory()->create(['workspace_id' => $workspace->id]);

        return [$user, $workspace, $project];
    }

    public function test_user_can_create_task(): void
    {
        [$user, $workspace, $project] = $this->createSetup();

        $response = $this->actingAs($user)
            ->postJson("/api/projects/{$project->id}/tasks", [
                'title'    => 'Fix the login bug',
                'priority' => 'high',
                'status'   => 'todo',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Fix the login bug');

        $this->assertDatabaseHas('tasks', ['title' => 'Fix the login bug']);
    }

    public function test_user_can_list_tasks_in_project(): void
    {
        [$user, $workspace, $project] = $this->createSetup();

        Task::factory()->count(4)->create(['project_id' => $project->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/projects/{$project->id}/tasks");

        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    public function test_user_can_update_task_status(): void
    {
        [$user, $workspace, $project] = $this->createSetup();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($user)
            ->putJson("/api/tasks/{$task->id}", [
                'status' => 'in_progress',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'in_progress');
    }

    public function test_user_can_delete_task(): void
    {
        [$user, $workspace, $project] = $this->createSetup();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_outsider_cannot_access_tasks(): void
    {
        $outsider  = User::factory()->create();
        [$user, $workspace, $project] = $this->createSetup();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($outsider)
            ->getJson("/api/projects/{$project->id}/tasks");

        $response->assertStatus(403);
    }

    public function test_task_requires_title(): void
    {
        [$user, $workspace, $project] = $this->createSetup();

        $response = $this->actingAs($user)
            ->postJson("/api/projects/{$project->id}/tasks", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }
}
