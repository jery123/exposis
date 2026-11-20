<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserRoleController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $users = User::with('accessRole')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $request->string('q')->toString()).'%';
                $query->where(fn ($users) => $users
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'users' => $users->getCollection()->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'role_label' => $user->roleLabel(),
                    'is_admin' => $user->hasPermissionTo('admin.access'),
                    'is_current_user' => $request->user()->is($user),
                ])->values(),
                'roles' => $roles->map(fn (Role $role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                ])->values(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'total' => $users->total(),
                ],
                'status_message' => session('status'),
                'errors' => session('errors')?->getBag('default')->messages() ?? [],
                'old' => ['role_id' => old('role_id')],
            ]);
        }

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);
        $role = Role::findOrFail($data['role_id']);

        if ($request->user()->is($user) && $user->role_id !== $role->id) {
            throw ValidationException::withMessages([
                'role_id' => 'Vous ne pouvez pas modifier votre propre rôle.',
            ]);
        }

        $currentlyAdmin = $user->hasPermissionTo('admin.access');
        $willBeAdmin = $role->permissions()->where('slug', 'admin.access')->exists();
        $otherAdminCount = User::whereKeyNot($user->getKey())
            ->where(fn ($users) => $users
                ->whereHas('accessRole.permissions', fn ($query) => $query->where('slug', 'admin.access'))
                ->orWhere(fn ($legacy) => $legacy->whereNull('role_id')->where('role', 'admin')))
            ->count();

        if ($currentlyAdmin && ! $willBeAdmin && $otherAdminCount === 0) {
            throw ValidationException::withMessages([
                'role_id' => 'Le dernier administrateur doit rester actif.',
            ]);
        }

        $user->forceFill([
            'role_id' => $role->id,
            'role' => $role->slug,
        ])->save();

        return back()->with('status', 'Rôle attribué à '.$user->name.'.');
    }
}
