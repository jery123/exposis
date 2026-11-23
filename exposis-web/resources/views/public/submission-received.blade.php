@extends('layouts.public')
@section('title', 'Demande reçue | EXPOSIS')
@section('robots', 'noindex, nofollow')
@section('content')
    <section class="receipt-page">
        <span class="receipt-mark" aria-hidden="true">✓</span>
        <p class="section-kicker"><span>Demande envoyée</span> Merci pour votre confiance</p>
        <h1>Votre idée est<br><em>entre de bonnes mains.</em></h1>
        <p>Notre équipe va l’étudier et vous recontactera à l’adresse indiquée. Aucun droit de propriété intellectuelle n’est transféré par cette soumission.</p>
        <p class="receipt-reference">Votre référence : <strong>{{ $reference }}</strong></p>
        <div class="receipt-actions"><a class="button button-dark" href="{{ route('home') }}">Retour à l’accueil <span aria-hidden="true">↗</span></a><a class="text-link" href="{{ route('public.projects.index') }}">Découvrir les projets</a></div>
    </section>
@endsection
