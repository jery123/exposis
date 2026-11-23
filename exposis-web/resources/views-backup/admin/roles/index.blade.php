<x-admin-layout title="Rôles et permissions">
  @if (session('status'))<p class="admin-alert admin-alert--success" role="status">{{ session('status') }}</p>@endif
  @if ($errors->has('role'))<p class="admin-alert admin-alert--error" role="alert">{{ $errors->first('role') }}</p>@endif
  <section class="admin-page-heading">
    <div><span class="admin-eyebrow">Accès et équipe</span><h2>Rôles et permissions</h2><p>Définissez précisément les espaces et les actions accessibles à chaque rôle.</p></div>
    <a class="admin-button admin-button--primary" href="{{ route('admin.roles.create') }}">Créer un rôle <span aria-hidden="true">＋</span></a>
  </section>

  <div class="admin-role-grid">
    @forelse ($roles as $role)
      <article class="admin-card admin-role-card">
        <div class="admin-role-card__top">
          <div><span class="admin-role-card__mark" aria-hidden="true">{{ $role->is_system ? '✦' : '◈' }}</span><div><h3>{{ $role->name }}</h3><span class="admin-muted">{{ $role->users_count }} compte(s)</span></div></div>
          @if ($role->is_system)<span class="admin-pill">Rôle système</span>@endif
        </div>
        <p class="admin-role-card__description">{{ $role->description ?: 'Aucune description renseignée.' }}</p>
        <div class="admin-role-card__permissions">
          <span class="admin-eyebrow">Permissions</span>
          @forelse ($role->permissions as $permission)
            <span>{{ $permission->name }}</span>
          @empty
            <span class="admin-muted">Aucune permission administrative</span>
          @endforelse
        </div>
        <div class="admin-role-card__actions">
          @if ($role->is_system)
            <span class="admin-muted">Permissions protégées</span>
          @else
            <a class="admin-text-link" href="{{ route('admin.roles.edit', $role) }}">Modifier le rôle ↗</a>
            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Supprimer le rôle « {{ $role->name }} » ?')">
              @csrf @method('DELETE')
              <button class="admin-danger-link" type="submit">Supprimer</button>
            </form>
          @endif
        </div>
      </article>
    @empty
      <div class="admin-empty">Aucun rôle n’est encore configuré.</div>
    @endforelse
  </div>
</x-admin-layout>
