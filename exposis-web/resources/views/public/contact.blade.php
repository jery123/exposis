@extends('layouts.public')
@section('title', 'Contact | EXPOSIS')
@section('description', 'Contactez EXPOSIS ou soumettez une idée pour une étude de faisabilité, un prototype ou une collaboration de recherche.')
@section('content')
    <section class="portal">
        <div class="portal__inner">
            <header class="portal__heading"><span class="portal__eyebrow">EXPOSIS · Contact</span><h1>Parlons de votre idée.</h1><p>Soumettre un projet ne transfère aucun droit. Un accord ou un NDA précède tout travail approfondi.</p></header>
            <div class="portal__grid">
                <section class="portal__card"><h2>Soumettre un projet</h2><p>Créez un compte pour déposer votre projet et suivre son évaluation de faisabilité.</p><a class="portal__button portal__button--accent" href="{{ route('public.submissions.create') }}">Soumettre un projet</a></section>
                <section class="portal__card"><h2>Coordonnées</h2><p><a class="portal__link" href="mailto:contact-lab@exposis.net">contact-lab@exposis.net</a></p><p>[À compléter] Côte d’Ivoire</p><p>Réseaux sociaux : comptes à venir.</p></section>
            </div>
        </div>
    </section>
@endsection
