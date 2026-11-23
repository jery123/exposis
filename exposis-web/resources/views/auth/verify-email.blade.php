@extends('layouts.public')
@section('title', 'Vérifier votre adresse e-mail | EXPOSIS')
@section('robots', 'noindex, nofollow')
@section('content')
    <section class="portal"><div class="portal__inner">
        <header class="portal__heading"><span class="portal__eyebrow">Espace membre</span><h1>Vérifiez votre adresse e-mail.</h1><p>Confirmez votre adresse depuis le lien envoyé par e-mail pour accéder à toutes les fonctionnalités du compte.</p></header>
        @if (session('status') === 'verification-link-sent')<p class="portal__message" role="status">Un nouveau lien de vérification vient d’être envoyé.</p>@endif
        <form action="{{ route('verification.send') }}" class="portal__form" method="POST">@csrf<button class="portal__button portal__button--accent" type="submit">Renvoyer le lien de vérification</button></form>
        <form action="{{ route('logout') }}" class="portal__form" method="POST">@csrf<button class="portal__button" type="submit">Se déconnecter</button></form>
    </div></section>
@endsection
