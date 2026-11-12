<x-admin-layout :title="$role->exists ? 'Modifier un rôle' : 'Créer un rôle'">
  <a class="admin-back-link" href="{{ route('admin.roles.index') }}">← Retour aux rôles</a>
  <section class="admin-page-heading">
    <div><span class="admin-eyebrow">Accès et équipe</span><h2>{{ $role->exists ? 'Modifier « '.$role->name.' »' : 'Créer un rôle' }}</h2><p>Choisissez les permissions nécessaires, en appliquant le principe du moindre privilège.</p></div>
  </section>
  <form class="admin-form-card" method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
    @csrf
    @if ($role->exists) @method('PUT') @endif
    <div class="admin-form-field">
      <label for="name">Nom du rôle</label>
      <input id="name" name="name" maxlength="80" value="{{ old('name', $role->name) }}" required>
      @error('name')<span class="admin-field-error">{{ $message }}</span>@enderror
    </div>
    <div class="admin-form-field">
      <label for="description">Description</label>
      <textarea id="description" name="description" rows="3" maxlength="500">{{ old('description', $role->description) }}</textarea>
      @error('description')<span class="admin-field-error">{{ $message }}</span>@enderror
    </div>
    <fieldset class="admin-permission-fieldset">
      <legend>Permissions administratives</legend>
      @error('permission_ids')<span class="admin-field-error">{{ $message }}</span>@enderror
      @foreach ($permissions as $category => $items)
        <div class="admin-permission-group">
          <h3>{{ $category }}</h3>
          @foreach ($items as $permission)
            <label class="admin-permission-option">
              <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}"
                @checked(in_array($permission->id, array_map('intval', old('permission_ids', $role->permissions->modelKeys())), true))>
              <span><strong>{{ $permission->name }}</strong><small>{{ $permission->slug }}</small></span>
            </label>
          @endforeach
        </div>
      @endforeach
      @error('permission_ids.*')<span class="admin-field-error">{{ $message }}</span>@enderror
    </fieldset>
    <div class="admin-form-actions">
      <a class="admin-button admin-button--secondary" href="{{ route('admin.roles.index') }}">Annuler</a>
      <button class="admin-button admin-button--primary" type="submit">{{ $role->exists ? 'Enregistrer les changements' : 'Créer le rôle' }} <span aria-hidden="true">↗</span></button>
    </div>
  </form>
</x-admin-layout>
