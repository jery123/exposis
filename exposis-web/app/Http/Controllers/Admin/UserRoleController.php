<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserRoleController extends Controller
{
    public function index(Request $request): View
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

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
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
        $adminCount = User::whereHas(
            'accessRole.permissions',
            fn ($query) => $query->where('slug', '!=', 'admin.access')
        )->count();

        if ($currentlyAdmin && ! $willBeAdmin && $adminCount <= 1) {
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
