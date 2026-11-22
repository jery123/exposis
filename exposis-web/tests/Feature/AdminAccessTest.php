<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_workspace_has_dedicated_navigation_and_role_management(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => $adminRole->id]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Vue d’ensemble')
            ->assertSee('Rôles et permissions')
            ->assertSee('Comptes membres');

        $this->actingAs(User::factory()->create())
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_admin_project_forms_render_the_responsive_workspace_layout(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => $adminRole->id]);

        $this->actingAs($admin)
            ->get(route('admin.public-projects.create'))
            ->assertOk()
            ->assertSee('admin-project-form', false)
            ->assertSee('admin-form-grid--two', false)
            ->assertSee('name="publish"', false);

        $project = $admin->projects()->create([
            'title' => 'Projet à examiner',
            'request_type' => 'feasibility',
            'problem' => 'Un problème concret à résoudre.',
            'description' => 'Une description détaillée du projet.',
        ]);

        $this->get(route('admin.projects.show', $project))
            ->assertOk()
            ->assertSee('admin-project-detail', false)
            ->assertSee('admin-evaluation-grid', false)
            ->assertSee('name="technical"', false);
    }

    public function test_admin_can_filter_user_profiles_by_search_and_role(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => $adminRole->id]);
        $visitorRole = Role::where('slug', 'visitor')->firstOrFail();
        $matchingUser = User::factory()->create([
            'name' => 'Awa Kouame',
            'email' => 'awa@example.test',
            'organization_name' => 'Recherche Exposis',
            'role' => 'visitor',
            'role_id' => $visitorRole->id,
        ]);
        User::factory()->create([
            'name' => 'Koffi Yao',
            'email' => 'koffi@example.test',
            'role' => 'visitor',
            'role_id' => $visitorRole->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['q' => 'Recherche Exposis', 'role_id' => $visitorRole->id]))
            ->assertOk()
            ->assertSee('Awa Kouame')
            ->assertDontSee('Koffi Yao')
            ->assertSee(route('admin.users.export', ['q' => 'Recherche Exposis', 'role_id' => $visitorRole->id]));

        $this->assertSame($visitorRole->id, $matchingUser->fresh()->role_id);
    }

    public function test_admin_can_export_user_profiles_with_the_applied_filters(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => $adminRole->id]);
        $visitorRole = Role::where('slug', 'visitor')->firstOrFail();
        User::factory()->create([
            'name' => '=HYPERLINK("https://example.test")',
            'email' => 'exported@example.test',
            'organization_name' => 'Organisation exportée',
            'phone' => '+22501020304',
            'role' => 'visitor',
            'role_id' => $visitorRole->id,
        ]);
        User::factory()->create([
            'name' => 'Compte à exclure',
            'email' => 'excluded@example.test',
            'role' => 'member',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.export', ['role_id' => $visitorRole->id]))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertDownload('exposis-comptes-'.now()->format('Ymd').'.csv');

        $csv = $response->streamedContent();
        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringContainsString('exported@example.test', $csv);
        $this->assertStringNotContainsString('excluded@example.test', $csv);
    }

    public function test_dashboard_sends_admins_to_their_workspace_and_members_to_their_own(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => $adminRole->id]);
        $member = User::factory()->create();

        $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($member)->get(route('dashboard'))->assertRedirect(route('projects.index'));
    }

    public function test_admin_can_create_a_role_with_selected_permissions(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => $adminRole->id]);
        $permission = Permission::where('slug', 'projects.manage')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.roles.store'), [
            'name' => 'Responsable des projets',
            'description' => 'Examine les demandes de projet.',
            'permission_ids' => [$permission->id],
        ])->assertRedirect(route('admin.roles.index'));

        $role = Role::where('slug', 'responsable-des-projets')->firstOrFail();
        $this->assertTrue($role->permissions->contains('slug', 'projects.manage'));
        $this->assertFalse($role->permissions->contains('slug', 'roles.manage'));
    }

    public function test_assigned_permissions_are_enforced_on_admin_routes(): void
    {
        $permission = Permission::where('slug', 'community_projects.manage')->firstOrFail();
        $role = Role::create([
            'name' => 'Modération communauté',
            'slug' => 'moderation-communaute',
            'description' => 'Gère le catalogue communautaire.',
        ]);
        $role->permissions()->sync([
            $permission->id,
            Permission::where('slug', 'admin.access')->value('id'),
        ]);
        $member = User::factory()->create(['role' => $role->slug, 'role_id' => $role->id]);

        $this->actingAs($member)->get(route('admin.public-projects.index'))->assertOk();
        $this->actingAs($member)->get(route('admin.projects.index'))->assertForbidden();
        $this->actingAs($member)->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_a_visitor_download_permission_does_not_grant_admin_access(): void
    {
        $role = Role::where('slug', 'visitor')->firstOrFail();
        $visitor = User::factory()->create(['role' => 'visitor', 'role_id' => $role->id]);

        $this->assertTrue($visitor->hasPermissionTo('community_projects.download_pdf'));
        $this->assertFalse($visitor->hasPermissionTo('admin.access'));
        $this->actingAs($visitor)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_cannot_remove_their_own_role_or_the_last_administrator(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $memberRole = Role::where('slug', 'member')->firstOrFail();
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => $adminRole->id]);

        $this->actingAs($admin)
            ->patch(route('admin.users.role.update', $admin), ['role_id' => $memberRole->id])
            ->assertSessionHasErrors('role_id');

        $otherAdmin = User::factory()->create(['role' => 'admin', 'role_id' => $adminRole->id]);
        $this->actingAs($admin)
            ->patch(route('admin.users.role.update', $otherAdmin), ['role_id' => $memberRole->id])
            ->assertSessionHasNoErrors();
    }

    public function test_admin_can_assign_a_role_to_another_account(): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();
        $admin = User::factory()->create(['role' => 'admin', 'role_id' => $adminRole->id]);
        $member = User::factory()->create();
        $role = Role::create([
            'name' => 'Gestionnaire des projets',
            'slug' => 'gestionnaire-des-projets',
        ]);
        $role->permissions()->sync([
            Permission::where('slug', 'projects.manage')->value('id'),
            Permission::where('slug', 'admin.access')->value('id'),
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.role.update', $member), ['role_id' => $role->id])
            ->assertSessionHasNoErrors();

        $this->assertSame($role->id, $member->fresh()->role_id);
        $this->actingAs($member->fresh())->get(route('admin.projects.index'))->assertOk();
        $this->actingAs($member->fresh())->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_role_manager_cannot_remove_the_last_administrative_permissions(): void
    {
        $role = Role::create([
            'name' => 'Administrateur d’équipe',
            'slug' => 'administrateur-equipe',
        ]);
        $role->permissions()->sync([
            Permission::where('slug', 'roles.manage')->value('id'),
            Permission::where('slug', 'dashboard.view')->value('id'),
            Permission::where('slug', 'admin.access')->value('id'),
        ]);
        $admin = User::factory()->create(['role' => $role->slug, 'role_id' => $role->id]);

        $this->actingAs($admin)
            ->put(route('admin.roles.update', $role), [
                'name' => $role->name,
                'description' => '',
                'permission_ids' => [],
            ])
            ->assertSessionHasErrors('permission_ids');

        $this->assertSame(3, $role->fresh()->permissions()->count());
    }
}
