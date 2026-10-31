@php
  $msgs = $project->messages()->with('user')->when(! $admin, fn ($q) => $q->where('is_internal', false))->get();
  $labels = ['submitted' => 'Projet soumis', 'status' => 'Statut modifié', 'nda' => 'NDA modifié', 'file' => 'Fichier ajouté'];
@endphp
<div class="space-y-6">
  <section class="rounded-2xl border bg-white p-6" aria-labelledby="msg-h">
    <h2 id="msg-h" class="font-extrabold">Échanges</h2>
    <ul class="mt-4 space-y-3">
      @forelse ($msgs as $m)
        <li class="rounded-xl p-3 {{ $m->is_internal ? 'bg-yellow-50 border border-yellow-300' : ($m->user_id === $project->user_id ? 'bg-gray-100' : 'bg-orange-50') }}">
          <p class="text-xs text-gray-600"><b>{{ $m->user->role === 'admin' ? 'EXPOSIS' : ($m->user->organization_name ?? $m->user->name) }}</b>@if($m->is_internal) · note interne @endif · {{ $m->created_at->translatedFormat('j M Y H:i') }}</p>
          <p class="mt-1 whitespace-pre-line">{{ $m->body }}</p></li>
      @empty <li class="text-gray-600">Aucun message pour l’instant.</li>
      @endforelse
    </ul>
    <form method="POST" action="{{ route('projects.messages.store', $project) }}" class="mt-4 grid gap-3">@csrf
      <label for="body" class="sr-only">Votre message</label>
      <textarea id="body" name="body" rows="3" maxlength="3000" class="w-full rounded-xl border-gray-400" required></textarea>
      @error('body')<p class="text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
      @if ($admin)<label class="flex gap-2 text-sm"><input type="checkbox" name="is_internal" value="1"> Note interne (invisible pour l’organisation)</label>@endif
      <div><button class="rounded-xl bg-gray-900 px-4 py-2 text-white">Envoyer</button></div></form>
  </section>

  <section class="rounded-2xl border bg-white p-6" aria-labelledby="file-h">
    <h2 id="file-h" class="font-extrabold">Documents</h2>
    <ul class="mt-3 divide-y">
      @forelse ($project->files as $f)<li class="py-2"><a class="font-semibold underline" href="{{ route('files.download', $f) }}">{{ $f->original_name }}</a> <small class="text-gray-600">({{ number_format($f->size / 1024, 0) }} Ko)</small></li>
      @empty <li class="py-2 text-gray-600">Aucun document.</li>@endforelse
    </ul>
    <form method="POST" enctype="multipart/form-data" action="{{ route('projects.files.store', $project) }}" class="mt-4 flex flex-wrap items-center gap-3">@csrf
      <label for="files" class="sr-only">Ajouter des documents</label>
      <input id="files" type="file" name="files[]" multiple accept=".pdf,.png,.jpg,.jpeg,.docx,.xlsx,.pptx,.txt,.zip">
      <button class="rounded-xl bg-gray-900 px-4 py-2 text-white">Téléverser</button>
      <p class="w-full text-xs text-gray-600">5 fichiers max, 5 Mo chacun. Stockage privé, accessible uniquement à votre organisation et à EXPOSIS.</p>
      @error('files')<p class="text-sm text-red-700" role="alert">{{ $message }}</p>@enderror @error('files.*')<p class="text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    </form>
  </section>

  <section class="rounded-2xl border bg-white p-6" aria-labelledby="ev-h">
    <h2 id="ev-h" class="font-extrabold">Historique</h2>
    <ol class="mt-3 space-y-1 text-sm">
      @foreach ($project->events as $e)
        <li>{{ $e->created_at->translatedFormat('j M Y H:i') }} · {{ $labels[$e->type] ?? $e->type }}@if($e->to) :
          {{ $e->type === 'status' ? (\App\Models\Project::STATUSES[$e->to] ?? $e->to) : ($e->type === 'nda' ? (\App\Models\Project::NDA[$e->to] ?? $e->to) : '') }}@endif</li>
      @endforeach
    </ol>
  </section>
</div>
