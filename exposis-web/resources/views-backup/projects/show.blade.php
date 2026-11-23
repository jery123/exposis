<x-app-layout>
  <x-slot name="header"><h1 class="text-xl font-extrabold">{{ $project->title }}</h1></x-slot>
  <div class="mx-auto max-w-3xl space-y-6 p-6">
    @if (session('status'))<p class="rounded-lg bg-orange-100 p-3 text-sm font-medium text-orange-900" role="status">{{ session('status') }}</p>@endif
    <p class="text-sm text-gray-600">{{ $project->reference }} · {{ $project->typeLabel() }} · <b>{{ $project->statusLabel() }}</b> · NDA : {{ \App\Models\Project::NDA[$project->nda_status] }}</p>
    <section class="rounded-2xl border bg-white p-6"><h2 class="font-extrabold">Problème</h2><p class="mt-2 whitespace-pre-line">{{ $project->problem }}</p>
      <h2 class="mt-6 font-extrabold">Description</h2><p class="mt-2 whitespace-pre-line">{{ $project->description }}</p></section>
    <section class="rounded-2xl border bg-white p-6">
      <h2 class="font-extrabold">Échanges avec EXPOSIS</h2>
      <div class="mt-4 space-y-3">
        @forelse ($project->messages->where('is_internal', false) as $message)
          <article class="rounded-xl bg-gray-50 p-4">
            <p class="text-sm font-semibold">{{ $message->user?->name ?? 'Équipe EXPOSIS' }} · {{ $message->created_at->format('d/m/Y H:i') }}</p>
            <p class="mt-2 whitespace-pre-line">{{ $message->body }}</p>
          </article>
        @empty
          <p class="text-gray-600">Aucun échange pour le moment.</p>
        @endforelse
      </div>
      <form method="POST" action="{{ route('projects.messages.store', $project) }}" class="mt-5">
        @csrf
        <label for="message" class="block font-semibold">Votre message</label>
        <textarea id="message" name="body" rows="3" maxlength="3000" class="mt-2 w-full rounded-xl border-gray-300" required>{{ old('body') }}</textarea>
        <x-input-error :messages="$errors->get('body')" class="mt-2" />
        <button class="mt-3 rounded-xl bg-gray-900 px-4 py-2.5 font-semibold text-white">Envoyer</button>
      </form>
    </section>
    @if ($project->evaluation?->shared)
      <section class="rounded-2xl border-2 border-orange-600 bg-white p-6"><h2 class="font-extrabold">Évaluation de faisabilité</h2>
        <p class="mt-2 text-lg"><b>{{ \App\Models\Evaluation::RECOMMENDATIONS[$project->evaluation->recommendation] }}</b> · score {{ $project->evaluation->score() }}/100</p>
        <p class="mt-3 whitespace-pre-line">{{ $project->evaluation->summary }}</p></section>
    @endif
  </div>
</x-app-layout>
