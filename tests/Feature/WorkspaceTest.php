<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_workspace(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/workspaces', [
                'name' => 'My Company',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'My Company');

        $this->assertDatabaseHas('workspaces', ['name' => 'My Company']);
    }

    public function test_user_can_list_their_workspaces(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();

        $workspace      = Workspace::factory()->create(['owner_id' => $user->id]);
        $otherWorkspace = Workspace::factory()->create(['owner_id' => $other->id]);

        $workspace->members()->attach($user->id, ['role' => 'owner']);

        $response = $this->actingAs($user)
            ->getJson('/api/workspaces');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_user_cannot_see_workspace_they_dont_belong_to(): void
    {
        $user      = User::factory()->create();
        $other     = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $other->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/workspaces/{$workspace->id}");

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_workspace(): void
    {
        $user      = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
        $workspace->members()->attach($user->id, ['role' => 'owner']);

        $response = $this->actingAs($user)
            ->deleteJson("/api/workspaces/{$workspace->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('workspaces', ['id' => $workspace->id]);
    }

    public function test_non_owner_cannot_delete_workspace(): void
    {
        $owner  = User::factory()->create();
        $member = User::factory()->create();

        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        $workspace->members()->attach($member->id, ['role' => 'member']);

        $response = $this->actingAs($member)
            ->deleteJson("/api/workspaces/{$workspace->id}");

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_workspaces(): void
    {
        $response = $this->getJson('/api/workspaces');
        $response->assertStatus(401);
    }
}
