@extends('layouts.public')
@section('title', 'Créer un compte | EXPOSIS')
@section('description', 'Rejoignez la communauté EXPOSIS en créant votre espace membre.')
@section('robots', 'noindex, nofollow')
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
                <div class="auth-heading"><span class="auth-step">Rejoindre EXPOSIS · 02</span><h2 id="auth-title">Faisons connaissance.</h2><p>Créez votre espace membre pour contribuer aux projets de la communauté.</p></div>
                @if ($errors->any())<p class="auth-message auth-message-error" role="alert">Vérifiez les champs signalés puis réessayez.</p>@endif
                <form action="{{ route('register', absolute: false) }}" class="auth-form" method="POST">
                    @csrf
                    <div class="auth-field"><label for="name">Nom complet</label><input aria-describedby="@error('name') name-error @enderror" autocomplete="name" autofocus id="name" name="name" required value="{{ old('name') }}">@error('name')<p class="auth-error" id="name-error">{{ $message }}</p>@enderror</div>
                    <div class="auth-field"><label for="organization_name">Organisation <span>(obligatoire)</span></label><input aria-describedby="@error('organization_name') organization_name-error @enderror" autocomplete="organization" id="organization_name" name="organization_name" required value="{{ old('organization_name') }}">@error('organization_name')<p class="auth-error" id="organization_name-error">{{ $message }}</p>@enderror</div>
                    <div class="auth-field"><label for="phone">Téléphone <span>(facultatif)</span></label><input aria-describedby="@error('phone') phone-error @enderror" autocomplete="tel" id="phone" name="phone" type="tel" value="{{ old('phone') }}">@error('phone')<p class="auth-error" id="phone-error">{{ $message }}</p>@enderror</div>
                    <div class="auth-field"><label for="email">Adresse e-mail</label><input aria-describedby="@error('email') email-error @enderror" autocomplete="username" id="email" name="email" required type="email" value="{{ old('email') }}">@error('email')<p class="auth-error" id="email-error">{{ $message }}</p>@enderror</div>
                    <div class="auth-field"><label for="password">Mot de passe</label><input aria-describedby="@error('password') password-error @enderror" autocomplete="new-password" id="password" name="password" required type="password">@error('password')<p class="auth-error" id="password-error">{{ $message }}</p>@enderror</div>
                    <div class="auth-field"><label for="password_confirmation">Confirmer le mot de passe</label><input aria-describedby="@error('password_confirmation') password_confirmation-error @enderror" autocomplete="new-password" id="password_confirmation" name="password_confirmation" required type="password">@error('password_confirmation')<p class="auth-error" id="password_confirmation-error">{{ $message }}</p>@enderror</div>
                    <button class="auth-submit" type="submit">Créer mon compte <span aria-hidden="true">↗</span></button>
                </form>
                <div class="auth-links"><p>Déjà membre ? <a href="{{ route('login', absolute: false) }}">Se connecter</a></p></div>
            </section>
        </div>
    </section>
@endsection
