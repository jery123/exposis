<x-app-layout>
  <x-slot name="header"><h1 class="text-xl font-extrabold">Mes projets communautaires</h1></x-slot>
  <div class="mx-auto max-w-5xl space-y-5 p-6">
    @if (session('status'))<p class="rounded-lg bg-orange-100 p-3 text-sm font-medium text-orange-900" role="status">{{ session('status') }}</p>@endif
    <div class="flex flex-wrap items-center justify-between gap-3">
      <p class="max-w-2xl text-gray-600">Partagez vos projets avec la communauté. Chaque proposition est relue par l’équipe avant sa publication.</p>
      <a href="{{ route('community.projects.create') }}" class="rounded-xl bg-orange-600 px-5 py-2.5 font-semibold text-white hover:bg-orange-700">Proposer un projet</a>
    </div>
    <ul class="divide-y rounded-2xl border bg-white">
      @forelse ($projects as $project)
        <li class="flex flex-wrap items-center justify-between gap-3 p-5">
          <div>
            <p class="font-bold">{{ $project->title }}</p>
            <p class="text-sm text-gray-600">{{ $project->pole }} · {{ $project->status }}</p>
          </div>
          @if ($project->published_at)
            <a class="rounded-full border border-emerald-700 px-3 py-1 text-sm font-semibold text-emerald-800" href="{{ route('public.projects.show', $project) }}">Publié · Voir le projet</a>
          @else
            <span class="rounded-full border border-orange-700 px-3 py-1 text-sm font-semibold text-orange-800">En cours de validation</span>
          @endif
        </li>
      @empty
        <li class="p-6 text-gray-600">Vous n’avez pas encore proposé de projet.</li>
      @endforelse
    </ul>
    <div>{{ $projects->links() }}</div>
  </div>
</x-app-layout>
