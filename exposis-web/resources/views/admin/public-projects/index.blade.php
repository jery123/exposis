<x-app-layout>
  <x-slot name="header"><h1 class="text-xl font-extrabold">Projets publics</h1></x-slot>
  <div class="mx-auto max-w-5xl p-6">
    @if (session('status'))<p class="mb-4 rounded-lg bg-orange-100 p-3 text-sm font-medium" role="status">{{ session('status') }}</p>@endif
    <a href="{{ route('admin.public-projects.create') }}" class="inline-block rounded-xl bg-orange-600 px-5 py-2.5 font-semibold text-white">Nouveau projet</a>
    <ul class="mt-6 divide-y rounded-2xl border bg-white">
      @foreach ($items as $i)<li class="flex flex-wrap items-center justify-between gap-3 p-4"><span><b>{{ $i->title }}</b> <small class="text-gray-600">{{ $i->pole }} · {{ $i->published_at ? 'publié' : 'à examiner' }}@if($i->user) · proposé par {{ $i->user->name }}@endif</small></span>
        <span class="flex gap-3"><a class="underline" href="{{ route('admin.public-projects.edit', $i) }}">Modifier</a>
        <form method="POST" action="{{ route('admin.public-projects.destroy', $i) }}" onsubmit="return confirm('Supprimer ce projet ?')">@csrf @method('DELETE')<button class="text-red-700 underline">Supprimer</button></form></span></li>@endforeach
    </ul><div class="mt-4">{{ $items->links() }}</div>
  </div>
</x-app-layout>
