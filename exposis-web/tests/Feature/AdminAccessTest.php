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
        $admin = User::factory()->create(['role' => 'admin']);

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

    public function test_admin_can_create_a_role_with_selected_permissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
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
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create();
        $role = Role::create([
            'name' => 'Gestionnaire des projets',
            'slug' => 'gestionnaire-des-projets',
        ]);
        $role->permissions()->sync([Permission::where('slug', 'projects.manage')->value('id')]);

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
        ]);
        $admin = User::factory()->create(['role' => $role->slug, 'role_id' => $role->id]);

        $this->actingAs($admin)
            ->put(route('admin.roles.update', $role), [
                'name' => $role->name,
                'description' => '',
                'permission_ids' => [],
            ])
            ->assertSessionHasErrors('permission_ids');

        $this->assertSame(2, $role->fresh()->permissions()->count());
    }
}
