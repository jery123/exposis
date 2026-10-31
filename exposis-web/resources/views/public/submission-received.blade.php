@extends('layouts.public')
@section('title', 'Demande reçue | EXPOSIS')
@section('content')
<section class="submission-success">
  <div class="w">
    <p class="eyb">Demande envoyée</p>
    <h1>Merci de nous avoir confié votre idée.</h1>
    <p>Notre équipe va l’étudier et vous recontactera à l’adresse indiquée. Aucun droit de propriété intellectuelle n’est transféré par cette soumission.</p>
    <p class="submission-reference">Votre référence : <strong>{{ $reference }}</strong></p>
    <a class="btn g" href="{{ route('home') }}">Retour à l’accueil</a>
  </div>
</section>
@endsection
