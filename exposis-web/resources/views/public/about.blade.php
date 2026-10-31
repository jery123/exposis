@extends('layouts.public')
@section('title', 'À propos | EXPOSIS, laboratoire de recherche informatique')
@section('description', 'EXPOSIS est une organisation à but non lucratif, indépendante, qui fait progresser les compétences et l’innovation informatique en Côte d’Ivoire.')
@section('content')

  <div class="ph"><div class="w"><h1>Un laboratoire pour apprendre en faisant.</h1><p class="lead">EXPOSIS, Laboratoire de Recherche, d’Expérimentation et d’Innovation en Informatique.</p></div></div>
  <section><div class="w two"><div><h2 class="h2">Notre mission.</h2></div><div>
    <p class="big">EXPOSIS est une organisation à but non lucratif, indépendante de tout parti politique et sans appartenance religieuse.</p>
    <p style="margin-top:20px" class="lead">Elle contribue aux compétences et à l’innovation informatique en Côte d’Ivoire, puis en Afrique, par la recherche appliquée, l’expérimentation et la diffusion de solutions numériques.</p></div></div></section>
  <section style="background:var(--mist)"><div class="w"><h2 class="h2">Ce qui nous distingue.</h2>
    <ul class="pri">
      <li><b>Recherche et expérimentation</b><span>Nous testons avant de conclure.</span></li><li><b>Production de compétence</b><span>Chaque projet doit faire grandir des compétences.</span></li>
      <li><b>Innovation utile</b><span>Nous partons de problèmes réels.</span></li><li><b>Partage des connaissances</b><span>Les résultats sont documentés et diffusés.</span></li>
      <li><b>Culture open source</b><span>Code ouvert chaque fois que c’est possible.</span></li><li><b>Éthique et responsabilité</b><span>Nous mesurons l’impact de ce que nous construisons.</span></li>
      <li><b>Indépendance</b><span>Aucune affiliation politique ou religieuse.</span></li></ul></div></section>
  <section class="dk" style="background:var(--ink);color:#fff"><div class="w two"><div><h2 class="h2" style="color:#fff">Indépendant, ouvert, utile.</h2></div><div><p class="big">Nous ne dépendons d’aucun parti ni d’aucune religion. Nos résultats sont documentés, et notre code est ouvert chaque fois que c’est possible.</p></div></div></section>
  <section class="cta"><div class="w"><h2>Travaillons ensemble.</h2><a class="btn" href="{{ route('public.submissions.create') }}">Soumettre un projet</a></div></section>
@endsection
