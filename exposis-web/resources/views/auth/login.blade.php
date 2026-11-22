@extends('layouts.public')
@section('title', 'Connexion membre | EXPOSIS')
@section('description', 'Connectez-vous à votre espace membre EXPOSIS.')
@section('content')
    <section class="auth-page">
        <div class="auth-content">
            <aside class="auth-aside">
                <span class="auth-kicker"><i></i> Recherche · Expérimentation · Innovation</span>
                <span aria-hidden="true" class="auth-infinity">∞</span>
                <h1>Les idées avancent<br>mieux <em>ensemble.</em></h1>
                <p>Un espace pour les membres qui apprennent, partagent et construisent des projets utiles avec EXPOSIS.</p>
                <div class="auth-aside-note"><span>EX / CI</span><span>Communauté de recherche · Côte d’Ivoire</span></div>
            </aside>
            <section aria-labelledby="auth-title" class="auth-panel">
                <span aria-hidden="true" class="auth-panel-mark">∞</span>
                <div class="auth-heading"><span class="auth-step">Espace membre · 01</span><h2 id="auth-title">Bon retour parmi nous.</h2><p>Connectez-vous pour retrouver vos projets et la communauté.</p></div>
                @if (session('status'))<p class="auth-message" role="status">{{ session('status') }}</p>@endif
                @if ($errors->any())<p class="auth-message auth-message-error" role="alert">Vérifiez les champs signalés puis réessayez.</p>@endif
                <form action="{{ route('login', absolute: false) }}" class="auth-form" method="POST">
                    @csrf
                    <div class="auth-field"><label for="email">Adresse e-mail</label><input aria-describedby="@error('email') email-error @enderror" autocomplete="username" autofocus id="email" name="email" required type="email" value="{{ old('email') }}">@error('email')<p class="auth-error" id="email-error">{{ $message }}</p>@enderror</div>
                    <div class="auth-field"><label for="password">Mot de passe</label><input aria-describedby="@error('password') password-error @enderror" autocomplete="current-password" id="password" name="password" required type="password">@error('password')<p class="auth-error" id="password-error">{{ $message }}</p>@enderror</div>
                    <label class="auth-check" for="remember"><input id="remember" name="remember" type="checkbox"><span>Se souvenir de moi</span></label>
                    <button class="auth-submit" type="submit">Se connecter <span aria-hidden="true">↗</span></button>
                </form>
                <div class="auth-links"><a href="{{ route('password.request', absolute: false) }}">Mot de passe oublié ?</a><p>Pas encore membre ? <a href="{{ route('register', absolute: false) }}">Créer un compte</a></p></div>
            </section>
        </div>
    </section>
@endsection
