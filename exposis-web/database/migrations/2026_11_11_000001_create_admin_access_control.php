<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug')->unique();
            $table->string('category', 80);
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        $now = now();
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Administrateur',
            'slug' => 'admin',
            'description' => 'Accès complet à l’espace d’administration.',
            'is_system' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $memberRoleId = DB::table('roles')->insertGetId([
            'name' => 'Membre',
            'slug' => 'member',
            'description' => 'Accès aux fonctionnalités réservées aux membres.',
            'is_system' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $permissions = [
            ['name' => 'Accéder à l’administration', 'slug' => 'admin.access', 'category' => 'Administration'],
            ['name' => 'Consulter le tableau de bord', 'slug' => 'dashboard.view', 'category' => 'Administration'],
            ['name' => 'Gérer les demandes de projet', 'slug' => 'projects.manage', 'category' => 'Projets reçus'],
            ['name' => 'Gérer les projets communautaires', 'slug' => 'community_projects.manage', 'category' => 'Projets communautaires'],
            ['name' => 'Gérer les comptes membres', 'slug' => 'users.manage', 'category' => 'Accès et équipe'],
            ['name' => 'Gérer les rôles et permissions', 'slug' => 'roles.manage', 'category' => 'Accès et équipe'],
        ];

        foreach ($permissions as &$permission) {
            $permission['created_at'] = $now;
            $permission['updated_at'] = $now;
        }
        unset($permission);

        DB::table('permissions')->insert($permissions);
        $permissionIds = DB::table('permissions')->pluck('id', 'slug');
        DB::table('permission_role')->insert(
            $permissionIds->map(fn (int $permissionId) => [
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
            ])->values()->all()
        );

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained()->restrictOnDelete();
        });

        DB::table('users')->where('role', 'admin')->update(['role_id' => $adminRoleId]);
        DB::table('users')->where(function ($query) {
            $query->whereNull('role')->orWhere('role', '!=', 'admin');
        })->update(['role_id' => $memberRoleId]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
