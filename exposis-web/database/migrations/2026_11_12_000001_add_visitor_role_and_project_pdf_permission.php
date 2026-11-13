<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $visitorRoleId = DB::table('roles')->where('slug', 'visitor')->value('id');
        if (! $visitorRoleId) {
            $visitorRoleId = DB::table('roles')->insertGetId([
                'name' => 'Visiteur',
                'slug' => 'visitor',
                'description' => 'Accès aux projets publics et à leurs fiches PDF.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $permissionId = DB::table('permissions')->where('slug', 'community_projects.download_pdf')->value('id');
        if (! $permissionId) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => 'Télécharger les fiches PDF des projets',
                'slug' => 'community_projects.download_pdf',
                'category' => 'Projets communautaires',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach (['admin', 'visitor'] as $slug) {
            $roleId = DB::table('roles')->where('slug', $slug)->value('id');
            if ($roleId) {
                DB::table('permission_role')->updateOrInsert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ], []);
            }
        }

        $memberRoleId = DB::table('roles')->where('slug', 'member')->value('id');
        DB::table('users')->where(function ($users) use ($memberRoleId) {
            $users->where('role', 'member');
            if ($memberRoleId) {
                $users->orWhere('role_id', $memberRoleId);
            }
        })->update([
            'role' => 'visitor',
            'role_id' => $visitorRoleId,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        $visitorRoleId = DB::table('roles')->where('slug', 'visitor')->value('id');
        $memberRoleId = DB::table('roles')->where('slug', 'member')->value('id');
        if ($visitorRoleId && $memberRoleId) {
            DB::table('users')->where('role_id', $visitorRoleId)->update([
                'role' => 'member',
                'role_id' => $memberRoleId,
                'updated_at' => now(),
            ]);
        }

        $permissionId = DB::table('permissions')->where('slug', 'community_projects.download_pdf')->value('id');
        if ($permissionId) {
            DB::table('permission_role')->where('permission_id', $permissionId)->delete();
            DB::table('permissions')->where('id', $permissionId)->delete();
        }

        if ($visitorRoleId) {
            DB::table('roles')->where('id', $visitorRoleId)->delete();
        }
    }
};