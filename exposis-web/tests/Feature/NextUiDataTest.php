<?php

namespace Tests\Feature;

use App\Models\PublicProject;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NextUiDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_screens_return_only_their_data_as_json(): void
    {
        $user = User::factory()->create();
        $project = $user->projects()->create([
            'title' => 'Projet membre',
            'request_type' => 'feasibility',
            'problem' => 'Un problème assez long.',
            'description' => 'Une description assez longue.',
        ]);
        $communityProject = $user->publicProjects()->create([
            'slug' => 'projet-communaute',
            'title' => 'Projet communautaire',
            'summary' => 'Un résumé.',
            'body' => 'Une description.',
            'pole' => PublicProject::POLES[0],
            'status' => 'Prototype',
            'team_members' => [],
            'is_sample' => false,
        ]);

        $this->actingAs($user)
            ->getJson('/projects')
            ->assertOk()
            ->assertJsonPath('projects.0.title', 'Projet membre')
            ->assertJsonPath('stats.total', 1);

        $this->getJson("/projects/{$project->id}")
            ->assertOk()
            ->assertJsonPath('project.title', 'Projet membre')
            ->assertJsonPath('project.reference', $project->reference);

        $this->getJson('/communaute/projets')
            ->assertOk()
            ->assertJsonPath('projects.0.slug', 'projet-communaute');

        $this->getJson('/communaute/projets/projet-communaute')
            ->assertOk()
            ->assertJsonPath('project.title', 'Projet communautaire');

        $this->getJson('/profile')
            ->assertOk()
            ->assertJsonPath('user.email', $user->email);

        $this->getJson('/projects/create')
            ->assertOk()
            ->assertJsonPath('types.feasibility', 'Étude de faisabilité technique');

        $this->getJson('/communaute/projets/nouveau')
            ->assertOk()
            ->assertJsonPath('poles.0', PublicProject::POLES[0]);
    }

    public function test_guest_password_screens_return_form_state_as_json(): void
    {
        $this->getJson('/forgot-password')
            ->assertOk()
            ->assertJsonStructure(['email', 'errors']);

        $this->getJson('/reset-password/test-token?email=person@example.com')
            ->assertOk()
            ->assertJsonPath('token', 'test-token')
            ->assertJsonPath('email', 'person@example.com');
    }

    public function test_admin_screens_return_json_for_authorized_roles(): void
    {
        $role = Role::where('slug', 'admin')->firstOrFail();
        $admin = User::factory()->create([
            'role' => 'admin',
            'role_id' => $role->id,
        ]);

        $this->actingAs($admin)
            ->getJson('/dashboard')
            ->assertOk()
            ->assertJsonPath('redirectTo', '/admin');

        $this->getJson('/admin')
            ->assertOk()
            ->assertJsonStructure(['counts', 'total', 'average_score', 'pending_public_projects', 'waiting']);

        $this->getJson('/admin/projects')
            ->assertOk()
            ->assertJsonStructure(['projects', 'pagination', 'statuses']);

        $this->getJson('/admin/public-projects')
            ->assertOk()
            ->assertJsonStructure(['items', 'pagination', 'poles', 'statuses']);

        $this->getJson('/admin/public-projects/create')
            ->assertOk()
            ->assertJsonStructure(['item', 'poles', 'statuses']);

        $this->getJson('/admin/roles')
            ->assertOk()
            ->assertJsonStructure(['roles']);

        $this->getJson('/admin/roles/create')
            ->assertOk()
            ->assertJsonStructure(['role', 'permissions']);

        $this->getJson('/admin/users')
            ->assertOk()
            ->assertJsonStructure(['users', 'roles', 'pagination']);
    }
}
