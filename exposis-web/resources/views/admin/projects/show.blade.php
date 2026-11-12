<x-admin-layout :title="'Demande '.$project->reference">
  @php $f = 'rounded-xl border-gray-400'; $ev = $project->evaluation; @endphp
  <div class="mx-auto grid max-w-6xl gap-6 p-6 lg:grid-cols-2">
    @if (session('status'))<p class="rounded-lg bg-orange-100 p-3 text-sm font-medium lg:col-span-2" role="status">{{ session('status') }}</p>@endif
    <section class="space-y-4 rounded-2xl border bg-white p-6">
      <p class="text-sm text-gray-600">
        {{ $project->user?->organization_name ?? $project->submitter_organization ?? 'Porteur de projet' }}
        · {{ $project->user?->name ?? $project->submitter_name }}
        · <a class="underline" href="mailto:{{ $project->user?->email ?? $project->submitter_email }}">{{ $project->user?->email ?? $project->submitter_email }}</a>
        · {{ $project->user?->phone ?? $project->submitter_phone ?? '—' }}
      </p>
      <p><b>{{ $project->typeLabel() }}</b> @if($project->confidential)· 🔒 confidentiel @endif · Délai : {{ $project->timeline ?? '—' }} · Budget : {{ $project->budget ?? '—' }}</p>
      <h2 class="font-extrabold">Problème</h2><p class="whitespace-pre-line">{{ $project->problem }}</p>
      <h2 class="font-extrabold">Description</h2><p class="whitespace-pre-line">{{ $project->description }}</p></section>
    <div class="space-y-6">
      <form method="POST" action="{{ route('admin.projects.update', $project) }}" class="flex flex-wrap items-end gap-3 rounded-2xl border bg-white p-6">@csrf @method('PATCH')
        <div><x-input-label for="status" value="Statut"/><select id="status" name="status" class="{{ $f }}">@foreach (\App\Models\Project::STATUSES as $k => $l)<option value="{{ $k }}" @selected($project->status === $k)>{{ $l }}</option>@endforeach</select></div>
        <div><x-input-label for="nda" value="NDA"/><select id="nda" name="nda_status" class="{{ $f }}">@foreach (\App\Models\Project::NDA as $k => $l)<option value="{{ $k }}" @selected($project->nda_status === $k)>{{ $l }}</option>@endforeach</select></div>
        <button class="rounded-xl bg-gray-900 px-4 py-2.5 text-white">Mettre à jour</button></form>
      <form method="POST" action="{{ route('admin.projects.evaluate', $project) }}" class="grid gap-4 rounded-2xl border-2 border-orange-600 bg-white p-6">@csrf @method('PUT')
        <h2 class="font-extrabold">Évaluation de faisabilité @if($ev)<span class="text-orange-700">· {{ $ev->score() }}/100</span>@endif</h2>
        <div class="grid grid-cols-2 gap-3">
          @foreach (['technical' => 'Faisabilité technique (5 = très faisable)', 'resources' => 'Ressources disponibles (5 = suffisantes)', 'impact' => 'Impact utile (5 = fort)', 'risk' => 'Risque (5 = élevé)'] as $k => $l)
            <div><x-input-label :for="$k" :value="$l"/><select id="{{ $k }}" name="{{ $k }}" class="{{ $f }} w-full">@for ($i = 1; $i <= 5; $i++)<option @selected(old($k, $ev?->$k) == $i)>{{ $i }}</option>@endfor</select></div>
          @endforeach</div>
        <div><x-input-label for="recommendation" value="Recommandation"/><select id="recommendation" name="recommendation" class="{{ $f }} w-full">@foreach (\App\Models\Evaluation::RECOMMENDATIONS as $k => $l)<option value="{{ $k }}" @selected(old('recommendation', $ev?->recommendation) === $k)>{{ $l }}</option>@endforeach</select></div>
        <div><x-input-label for="summary" value="Synthèse (visible par l’organisation si partagée)"/><textarea id="summary" name="summary" rows="6" class="{{ $f }} w-full" required>{{ old('summary', $ev?->summary) }}</textarea><x-input-error :messages="$errors->get('summary')"/></div>
        <label class="flex gap-3"><input type="checkbox" name="shared" value="1" @checked(old('shared', $ev?->shared))> Partager cette évaluation avec l’organisation</label>
        <button class="rounded-xl bg-orange-600 px-5 py-3 font-semibold text-white">Enregistrer l’évaluation</button></form>
    </div>
  </div>
</x-admin-layout>
