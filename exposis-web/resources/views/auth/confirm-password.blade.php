@extends('layouts.public')
@section('title', 'Confirmer votre identité | EXPOSIS')
@section('robots', 'noindex, nofollow')
@section('content')
    <section class="portal"><div class="portal__inner">
        <header class="portal__heading"><span class="portal__eyebrow">Espace membre</span><h1>Confirmer votre identité.</h1><p>Saisissez votre mot de passe pour continuer.</p></header>
        @if ($errors->any())<p class="portal-message portal-message--error" role="alert">{{ $errors->first() }}</p>@endif
        <form action="{{ route('password.confirm') }}" class="portal__form" method="POST">
            @csrf
            <label class="portal__field"><span class="portal__label">Mot de passe</span><input autocomplete="current-password" name="password" required type="password">@error('password')<span class="portal__error">{{ $message }}</span>@enderror</label>
            <button class="portal__button portal__button--accent" type="submit">Confirmer</button>
        </form>
    </div></section>
@endsection
