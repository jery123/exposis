<x-app-layout>
  <x-slot name="header"><h1 class="text-xl font-extrabold">Soumettre un projet</h1></x-slot>
  <div class="mx-auto max-w-3xl p-6">
    <p class="mb-6 text-gray-700">Soumettre un projet ne transfère aucun droit de propriété intellectuelle. Un accord ou un NDA précède tout travail approfondi.</p>
    <form method="POST" action="{{ route('projects.store') }}" class="grid gap-5 rounded-2xl border bg-white p-6">
      @csrf
      @php $f = 'w-full rounded-xl border-gray-400'; @endphp
      <div><x-input-label for="title" value="Titre du projet *"/><input id="title" name="title" maxlength="150" value="{{ old('title') }}" class="{{ $f }}" required><x-input-error :messages="$errors->get('title')"/></div>
      <div><x-input-label for="request_type" value="Type de demande *"/>
        <select id="request_type" name="request_type" class="{{ $f }}" required><option value="">Choisir…</option>
        @foreach (\App\Models\Project::TYPES as $k => $l)<option value="{{ $k }}" @selected(old('request_type') === $k)>{{ $l }}</option>@endforeach</select>
        <x-input-error :messages="$errors->get('request_type')"/></div>
      <div><x-input-label for="problem" value="Problème à résoudre * (2000 max)"/><textarea id="problem" name="problem" rows="4" maxlength="2000" class="{{ $f }}" required>{{ old('problem') }}</textarea><x-input-error :messages="$errors->get('problem')"/></div>
      <div><x-input-label for="description" value="Description du projet * (5000 max)"/><textarea id="description" name="description" rows="7" maxlength="5000" class="{{ $f }}" required>{{ old('description') }}</textarea><x-input-error :messages="$errors->get('description')"/></div>
      <div class="grid gap-5 sm:grid-cols-2">
        <div><x-input-label for="timeline" value="Délai souhaité"/><input id="timeline" name="timeline" value="{{ old('timeline') }}" class="{{ $f }}"></div>
        <div><x-input-label for="budget" value="Budget indicatif"/><input id="budget" name="budget" value="{{ old('budget') }}" class="{{ $f }}"></div></div>
      <div style="position:absolute;left:-9999px" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
      <label class="flex gap-3"><input type="checkbox" name="confidential" value="1" @checked(old('confidential')) class="mt-1"> Les informations de ce projet sont confidentielles.</label>
      <div><label class="flex gap-3"><input type="checkbox" name="consent" value="1" class="mt-1" required> J’accepte que ces données soient traitées pour répondre à ma demande. *</label><x-input-error :messages="$errors->get('consent')"/></div>
      <button class="rounded-xl bg-orange-600 px-5 py-3 font-semibold text-white hover:bg-orange-700">Envoyer mon projet</button>
    </form>
  </div>
</x-app-layout>
