<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private function createWorkspaceWithMember(): array
    {
        $user      = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $workspace->members()->attach($user->id, ['role' => 'owner']);

        return [$user, $workspace];
    }

    public function test_user_can_create_project(): void
    {
        [$user, $workspace] = $this->createWorkspaceWithMember();

        $response = $this->actingAs($user)
            ->postJson("/api/workspaces/{$workspace->id}/projects", [
                'name'        => 'My First Project',
                'description' => 'Project description',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'My First Project');

        $this->assertDatabaseHas('projects', ['name' => 'My First Project']);
    }

    public function test_user_can_list_projects_in_workspace(): void
    {
        [$user, $workspace] = $this->createWorkspaceWithMember();

        Project::factory()->count(3)->create(['workspace_id' => $workspace->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/workspaces/{$workspace->id}/projects");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_user_cannot_access_projects_in_workspace_they_dont_belong_to(): void
    {
        $outsider  = User::factory()->create();
        $owner     = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($outsider)
            ->getJson("/api/workspaces/{$workspace->id}/projects");

        $response->assertStatus(403);
    }

    public function test_user_can_update_project(): void
    {
        [$user, $workspace] = $this->createWorkspaceWithMember();
        $project = Project::factory()->create(['workspace_id' => $workspace->id]);

        $response = $this->actingAs($user)
            ->putJson("/api/projects/{$project->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name');
    }

    public function test_owner_can_delete_project(): void
    {
        [$user, $workspace] = $this->createWorkspaceWithMember();
        $project = Project::factory()->create(['workspace_id' => $workspace->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_non_member_cannot_delete_project(): void
    {
        $outsider  = User::factory()->create();
        $owner     = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $project   = Project::factory()->create(['workspace_id' => $workspace->id]);

        $response = $this->actingAs($outsider)
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(403);
    }
}
