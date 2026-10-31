<x-app-layout>
  <x-slot name="header"><h1 class="text-xl font-extrabold">Proposer un projet à la communauté</h1></x-slot>
  <div class="mx-auto max-w-3xl p-6">
    <p class="mb-6 text-gray-700">Présentez un projet porté par les membres d’EXPOSIS. Il restera privé jusqu’à validation par l’équipe.</p>
    <form method="POST" action="{{ route('community.projects.store') }}" class="grid gap-5 rounded-2xl border bg-white p-6">
      @csrf
      @php $f = 'w-full rounded-xl border-gray-400'; @endphp
      <div>
        <x-input-label for="title" value="Titre du projet *"/>
        <input id="title" name="title" maxlength="150" value="{{ old('title') }}" class="{{ $f }}" required>
        <x-input-error :messages="$errors->get('title')"/>
      </div>
      <div class="grid gap-5 sm:grid-cols-2">
        <div>
          <x-input-label for="pole" value="Domaine *"/>
          <select id="pole" name="pole" class="{{ $f }}" required>
            <option value="">Choisir…</option>
            @foreach (\App\Models\PublicProject::POLES as $pole)
              <option value="{{ $pole }}" @selected(old('pole') === $pole)>{{ $pole }}</option>
            @endforeach
          </select>
          <x-input-error :messages="$errors->get('pole')"/>
        </div>
        <div>
          <x-input-label for="status" value="Avancement *"/>
          <select id="status" name="status" class="{{ $f }}" required>
            @foreach (['Recherche', 'Prototype', 'MVP'] as $status)
              <option value="{{ $status }}" @selected(old('status', 'Prototype') === $status)>{{ $status }}</option>
            @endforeach
          </select>
          <x-input-error :messages="$errors->get('status')"/>
        </div>
      </div>
      <div>
        <x-input-label for="summary" value="Résumé * (400 caractères maximum)"/>
        <textarea id="summary" name="summary" rows="3" maxlength="400" class="{{ $f }}" required>{{ old('summary') }}</textarea>
        <x-input-error :messages="$errors->get('summary')"/>
      </div>
      <div>
        <x-input-label for="body" value="Description du projet *"/>
        <textarea id="body" name="body" rows="8" maxlength="50000" class="{{ $f }}" required>{{ old('body') }}</textarea>
        <x-input-error :messages="$errors->get('body')"/>
      </div>
      <div>
        <x-input-label for="team_members" value="Participants (un nom par ligne)"/>
        <textarea id="team_members" name="team_members" rows="4" maxlength="2000" class="{{ $f }}">{{ old('team_members') }}</textarea>
        <x-input-error :messages="$errors->get('team_members')"/>
      </div>
      <div>
        <x-input-label for="repo_url" value="Lien vers le code ou la documentation (facultatif)"/>
        <input id="repo_url" type="url" name="repo_url" value="{{ old('repo_url') }}" class="{{ $f }}">
        <x-input-error :messages="$errors->get('repo_url')"/>
      </div>
      <div>
        <label class="flex gap-3"><input type="checkbox" name="participant_consent" value="1" @checked(old('participant_consent')) required class="mt-1"> J’ai l’accord des participants pour partager leurs noms et je ne publie aucune information confidentielle. *</label>
        <x-input-error :messages="$errors->get('participant_consent')"/>
      </div>
      <button class="rounded-xl bg-orange-600 px-5 py-3 font-semibold text-white hover:bg-orange-700">Envoyer pour validation</button>
    </form>
  </div>
</x-app-layout>
