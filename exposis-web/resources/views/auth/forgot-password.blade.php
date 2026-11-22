@extends('layouts.public')
@section('title', 'Réinitialiser votre mot de passe | EXPOSIS')
@section('content')
    <section class="portal"><div class="portal__inner">
        <header class="portal__heading"><span class="portal__eyebrow">Espace membre</span><h1>Réinitialiser votre mot de passe.</h1><p>Nous vous enverrons un lien si cette adresse est associée à un compte.</p></header>
        @if (session('status'))<p class="portal__message" role="status">{{ session('status') }}</p>@endif
        <form action="{{ route('password.email') }}" class="portal__form" method="POST">
            @csrf
            <label class="portal__field"><span class="portal__label">Adresse e-mail</span><input autocomplete="email" name="email" required type="email" value="{{ old('email') }}">@error('email')<span class="portal__error">{{ $message }}</span>@enderror</label>
            <button class="portal__button portal__button--accent" type="submit">Envoyer le lien</button>
        </form>
        <p><a class="portal__link" href="{{ route('login') }}">Retour à la connexion</a></p>
    </div></section>
@endsection
