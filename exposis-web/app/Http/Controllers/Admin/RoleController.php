<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::with('permissions')->withCount('users')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.form', [
            'role' => new Role,
            'permissions' => Permission::orderBy('category')->orderBy('name')->get()->groupBy('category'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $slug = Str::slug($data['name']) ?: 'role-'.Str::lower(Str::random(8));

        if (Role::where('slug', $slug)->exists()) {
            return back()->withInput()->withErrors(['name' => 'Un rôle portant ce nom existe déjà.']);
        }

        $role = Role::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ]);
        $role->permissions()->sync($data['permission_ids'] ?? []);

        return redirect()->route('admin.roles.index')->with('status', 'Rôle créé.');
    }

    public function edit(Role $role): View
    {
        abort_if($role->is_system, 403);

        return view('admin.roles.form', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::orderBy('category')->orderBy('name')->get()->groupBy('category'),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->is_system, 403);

        $data = $this->validatedData($request);
        $permissionIds = $data['permission_ids'] ?? [];
        $roleHasAdminAccess = $role->permissions()->where('slug', '!=', 'admin.access')->exists();
        $willHaveAdminAccess = Permission::whereIn('id', $permissionIds)
            ->where('slug', '!=', 'admin.access')
            ->exists();
        $otherAdminCount = User::where('role_id', '!=', $role->id)
            ->whereHas('accessRole.permissions', fn ($query) => $query->where('slug', '!=', 'admin.access'))
            ->count();

        if ($roleHasAdminAccess && ! $willHaveAdminAccess && $otherAdminCount === 0 && $role->users()->exists()) {
            throw ValidationException::withMessages([
                'permission_ids' => 'Au moins un rôle actif doit conserver des permissions d’administration.',
            ]);
        }

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
        $role->permissions()->sync($permissionIds);

        return redirect()->route('admin.roles.index')->with('status', 'Rôle mis à jour.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->is_system, 403);

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'Ce rôle est attribué à des comptes. Réattribuez-les avant de le supprimer.']);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('status', 'Rôle supprimé.');
    }

    /**
     * @return array{name: string, description?: string|null, permission_ids?: array<int>}
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('roles', 'name')->ignore($request->route('role')?->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['required', 'integer', 'distinct', 'exists:permissions,id'],
        ]);
    }
}
