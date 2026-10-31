<x-guest-layout>
    <div class="auth-heading">
        <span class="auth-step">Rejoindre EXPOSIS · 02</span>
        <h2>Faisons connaissance.</h2>
        <p>Créez votre espace membre pour contribuer aux projets de la communauté.</p>
    </div>

    <form class="auth-form" method="POST" action="/register">
        @csrf
        <div class="auth-field">
            <label for="name">Nom complet</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" aria-describedby="@error('name') name-error @enderror">
            @error('name')<p class="auth-error" id="name-error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label for="organization_name">Organisation <span>(obligatoire)</span></label>
            <input id="organization_name" name="organization_name" type="text" value="{{ old('organization_name') }}" required autocomplete="organization" aria-describedby="@error('organization_name') organization-error @enderror">
            @error('organization_name')<p class="auth-error" id="organization-error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label for="phone">Téléphone <span>(facultatif)</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" aria-describedby="@error('phone') phone-error @enderror">
            @error('phone')<p class="auth-error" id="phone-error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label for="email">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" aria-describedby="@error('email') email-error @enderror">
            @error('email')<p class="auth-error" id="email-error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" aria-describedby="@error('password') password-error @enderror">
            @error('password')<p class="auth-error" id="password-error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" aria-describedby="@error('password_confirmation') password-confirmation-error @enderror">
            @error('password_confirmation')<p class="auth-error" id="password-confirmation-error">{{ $message }}</p>@enderror
        </div>
        <button class="auth-submit" type="submit">Créer mon compte <span aria-hidden="true">↗</span></button>
    </form>

    <div class="auth-links">
        <p>Déjà membre ? <a href="/login">Se connecter</a></p>
    </div>
</x-guest-layout>
