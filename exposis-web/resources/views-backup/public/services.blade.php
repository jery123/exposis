@extends('layouts.public')
@section('title', 'Services | Étude de faisabilité, prototype MVP, recherche | EXPOSIS')
@section('description', 'Étude de faisabilité technique, prototype MVP, collaboration de recherche et formation : les services du laboratoire EXPOSIS en Côte d’Ivoire.')
@section('content')

  <div class="ph"><div class="w"><h1>De l’idée au prototype.</h1><p class="lead">Quatre façons de travailler avec le laboratoire.</p></div></div>
  <section><div class="w"><div class="grid" id="sv" style="margin-top:0"></div></div></section>
  <section style="background:var(--mist)"><div class="w"><h2 class="h2">Comment ça marche.</h2>
    <ol class="steps"><li><b>Vous décrivez votre idée</b><span>Un formulaire court suffit pour commencer.</span></li><li><b>Nous évaluons la faisabilité</b><span>Contraintes, risques et pistes techniques.</span></li>
    <li><b>Nous construisons un prototype</b><span>Une première version (MVP) à tester.</span></li><li><b>Nous partageons les résultats</b><span>Documentation, enseignements, code ouvert si possible.</span></li></ol></div></section>
  <section><div class="w"><h2 class="h2">Questions fréquentes.</h2><div style="margin-top:36px;max-width:52rem">
    <details><summary>Est-ce que je perds des droits sur mon idée ?</summary><p>Non. Soumettre un projet ne transfère aucun droit de propriété intellectuelle.</p></details>
    <details><summary>Y a-t-il un accord de confidentialité ?</summary><p>Oui. Un accord ou un NDA précède tout travail approfondi. Vous pouvez aussi indiquer que votre projet est confidentiel.</p></details>
    <details><summary>Quels types de projets acceptez-vous ?</summary><p>Études de faisabilité, prototypes MVP et collaborations de recherche, en cybersécurité, intelligence artificielle, logiciel et open source, réseaux, cloud et edge, et formation.</p></details>
    <details><summary>Que se passe-t-il après ma demande ?</summary><p>Nous la lisons et vous répondons par e-mail. Précisez votre délai et votre budget indicatif : cela nous aide à cadrer la suite.</p></details></div></div></section>
  <section class="cta"><div class="w"><h2>Un projet en tête ?</h2><a class="btn" href="{{ route('public.submissions.create') }}">Soumettre un projet</a></div></section>
@endsection
