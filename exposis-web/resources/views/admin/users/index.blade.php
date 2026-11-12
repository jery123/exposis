<x-admin-layout title="Comptes membres">
  @if (session('status'))<p class="admin-alert admin-alert--success" role="status">{{ session('status') }}</p>@endif
  <section class="admin-page-heading">
    <div><span class="admin-eyebrow">Accès et équipe</span><h2>Comptes membres</h2><p>Attribuez un rôle à chaque compte. Les permissions du rôle déterminent ensuite les accès disponibles.</p></div>
    <span class="admin-count">{{ $users->total() }} compte(s)</span>
  </section>
  <form class="admin-search-form" method="GET">
    <label class="sr-only" for="user-search">Rechercher un membre</label>
    <input id="user-search" name="q" value="{{ request('q') }}" placeholder="Rechercher par nom ou adresse e-mail">
    <button class="admin-button admin-button--dark" type="submit">Rechercher <span aria-hidden="true">⌕</span></button>
  </form>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>Membre</th><th>Rôle actuel</th><th>Attribuer un rôle</th></tr></thead>
      <tbody>
        @forelse ($users as $user)
          <tr>
            <td><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></td>
            <td><span class="admin-pill {{ $user->hasPermissionTo('admin.access') ? 'admin-pill--accent' : '' }}">{{ $user->roleLabel() }}</span></td>
            <td>
              <form class="admin-role-assignment" method="POST" action="{{ route('admin.users.role.update', $user) }}">
                @csrf @method('PATCH')
                <label class="sr-only" for="role-{{ $user->id }}">Rôle de {{ $user->name }}</label>
                <select id="role-{{ $user->id }}" name="role_id" @disabled(Auth::user()->is($user))>
                  @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ $role->name }}</option>
                  @endforeach
                </select>
                <button class="admin-button admin-button--small" type="submit" @disabled(Auth::user()->is($user))>Enregistrer</button>
              </form>
              @if ($user->is(Auth::user()))<small class="admin-muted">Votre propre rôle ne peut pas être modifié ici.</small>@endif
            </td>
          </tr>
        @empty
          <tr><td colspan="3" class="admin-empty">Aucun compte ne correspond à cette recherche.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="admin-pagination">{{ $users->links() }}</div>
</x-admin-layout>
