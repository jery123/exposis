@extends('layouts.public')
@section('title', 'Contact | Soumettre un projet à EXPOSIS')
@section('description', 'Contactez EXPOSIS ou soumettez un projet pour une étude de faisabilité, un prototype MVP ou une collaboration de recherche.')
@section('content')
<div class="ph"><div class="w"><h1>Parlons de votre idée.</h1><p class="lead">Soumettre un projet ne transfère aucun droit. Un accord ou un NDA précède tout travail approfondi.</p></div></div>
<section><div class="w two">
  <div><h2 class="h2">Soumettre un projet.</h2><p class="lead" style="margin:18px 0 24px">Créez le compte de votre organisation pour déposer votre projet et suivre son évaluation de faisabilité.</p>
    <a class="btn o" href="{{ route('public.submissions.create') }}">Soumettre un projet</a> <a class="btn g" href="{{ route('login') }}">Connexion</a></div>
  <aside class="info" aria-label="Coordonnées">
    <div><h3>E-mail</h3><a href="mailto:contact-lab@exposis.net">contact-lab@exposis.net</a></div>
    <div><h3>Adresse</h3><p>[À compléter] Côte d’Ivoire</p></div>
    <div class="map"><b>Carte</b><br>La carte sera ajoutée avec l’adresse du laboratoire.</div>
    <div><h3>Réseaux sociaux</h3><p>Comptes à venir.</p></div></aside>
</div></section>
@endsection
