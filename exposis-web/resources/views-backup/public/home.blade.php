@extends('layouts.public')
@section('title', 'EXPOSIS | Laboratoire d’informatique en Côte d’Ivoire')
@section('description', 'EXPOSIS, laboratoire informatique ivoirien à but non lucratif : étude de faisabilité, prototype MVP et collaboration de recherche.')
@section('content')

  <div class="h2o"><div class="w">
    <p class="eyb">Recherche · Expérimentation · Innovation · Côte d’Ivoire</p>
    <h1>Ici, les idées <em>s’expérimentent.</em></h1>
    <p class="sub">Nous réunissons de jeunes diplômés, chercheurs et passionnés pour apprendre ensemble et construire des solutions utiles en Côte d’Ivoire et en Afrique.</p>
    <div class="row c"><a class="btn o" href="{{ route('public.submissions.create') }}">Soumettre un projet</a><a class="btn g" href="{{ route('public.projects.index') }}">Voir les projets</a></div>
    <div class="win" role="img" aria-label="Schéma du parcours d’un projet : décrit, évalué, prototypé, partagé">
      <div class="bar"><i></i><i></i><i></i><span>Parcours d’un projet</span></div>
      <div class="wb"><ul class="life"><li><b>Décrit</b><span>Votre idée et son problème</span></li><li><b>Évalué</b><span>Faisabilité technique</span></li><li><b>Prototypé</b><span>Première version (MVP)</span></li><li><b>Partagé</b><span>Documentation et code ouvert</span></li></ul>
      <svg viewBox="0 0 200 100" aria-hidden="true"><path pathLength="1" d="M100 50C130 5 190 5 190 50S130 95 100 50 10 5 10 50s60 45 90 0z"/></svg></div></div>
  </div></div>
  <section class="tight"><div class="w"><h2 class="mid">Cinq domaines de recherche</h2>
    <ul class="pills"><li>Cybersécurité</li><li>Intelligence artificielle</li><li>Logiciel et open source</li><li>Réseaux, cloud et edge</li><li>Formation</li></ul></div></section>
  <section style="background:var(--mist)"><div class="w"><h2 class="h2">Toute la flexibilité dont votre projet a besoin.</h2>
    <div class="grid3" id="fx"></div></div></section>
  <section><div class="w"><h2 class="h2">De l’idée aux résultats, au même endroit.</h2>
    <div class="tabs"><div class="tl" role="tablist" aria-label="Étapes" id="tl"></div><div class="tp" id="tp"></div></div></div></section>
  <section style="background:var(--mist)"><div class="w"><h2 class="h2">Pensé pour avancer sans frictions.</h2>
    <div class="g4"><div><h3>Rapide à démarrer</h3><p>Un formulaire court suffit : problème, description, délai et budget indicatif.</p></div>
    <div><h3>Pensé pour le terrain</h3><p>Réseau limité, coûts, infrastructure : ces contraintes sont prises en compte dès l’étude.</p></div>
    <div><h3>Ouvert par défaut</h3><p>Documentation, reproductibilité et code open source chaque fois que c’est possible.</p></div>
    <div><h3>Éthique et indépendance</h3><p>Organisation à but non lucratif, sans parti ni religion, qui mesure l’impact de ce qu’elle construit.</p></div></div></div></section>
  <section class="dk" style="background:var(--ink);color:#fff"><div class="w"><h2 class="h2" style="color:#fff">Deux façons de collaborer.</h2>
    <div class="aud"><article><span>Pour les porteurs de projet</span><h3>Vous avez une idée, nous vérifions qu’elle tient.</h3><p>Entreprises, associations, institutions ou particuliers : décrivez votre besoin et recevez une première évaluation.</p><a class="btn" href="{{ route('public.submissions.create') }}">Soumettre un projet</a></article>
    <article><span>Pour les chercheurs et partenaires</span><h3>Vous cherchez un terrain d’expérimentation.</h3><p>Universités, laboratoires, développeurs : collaborez sur des questions appliquées et partagez les résultats.</p><a class="btn g" href="{{ route('public.submissions.create') }}">Proposer une collaboration</a></article></div></div></section>
  <section><div class="w two"><div><h2 class="h2">Confidentialité par conception.</h2></div><div>
    <ul class="pri" style="margin-top:0"><li><b>Aucun droit transféré</b><span>Soumettre un projet ne transfère aucun droit de propriété intellectuelle.</span></li><li><b>NDA avant le fond</b><span>Un accord ou un NDA précède tout travail approfondi.</span></li><li><b>Données protégées</b><span>Traitement conforme à la loi ivoirienne n° 2013-450 (ARTCI). Détails à compléter dans les mentions légales.</span></li></ul></div></div></section>
  <section style="background:var(--mist)"><div class="w two"><div><h2 class="h2">Questions fréquentes.</h2></div><div>
    <details><summary>Qu’est-ce qu’EXPOSIS ?</summary><p>Un laboratoire de recherche, d’expérimentation et d’innovation en informatique, à but non lucratif, en Côte d’Ivoire.</p></details>
    <details><summary>Est-ce que je perds des droits sur mon idée ?</summary><p>Non. Soumettre un projet ne transfère aucun droit de propriété intellectuelle.</p></details>
    <details><summary>Quels projets acceptez-vous ?</summary><p>Études de faisabilité, prototypes MVP et collaborations de recherche dans nos cinq domaines.</p></details>
    <details><summary>Que se passe-t-il après ma demande ?</summary><p>Nous la lisons et vous répondons par e-mail. Un NDA précède tout travail approfondi.</p></details></div></div></section>
  <section class="cta"><div class="w"><h2>Mettez votre idée à l’épreuve.</h2><a class="btn" href="{{ route('public.submissions.create') }}">Soumettre un projet</a></div></section>
@endsection
