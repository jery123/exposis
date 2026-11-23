<x-guest-layout>
    <div class="auth-heading">
        <span class="auth-step">Accès au compte · Aide</span>
        <h2>On vous aide à revenir.</h2>
        <p>Indiquez l’adresse e-mail de votre compte. Nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>
    </div>
    @if (session('status'))
        <p class="auth-status" role="status">{{ session('status') }}</p>
    @endif
    <form class="auth-form" method="POST" action="/forgot-password">
        @csrf
        <div class="auth-field">
            <label for="email">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        </div>
        <button class="auth-submit" type="submit">Envoyer le lien de réinitialisation <span aria-hidden="true">↗</span></button>
    </form>
    <div class="auth-links"><p><a href="/login">← Retour à la connexion</a></p></div>
</x-guest-layout>
