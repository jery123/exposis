<x-guest-layout>
    <div class="auth-heading">
        <span class="auth-step">Espace membre · 01</span>
        <h2>Bon retour parmi nous.</h2>
        <p>Connectez-vous pour retrouver vos projets et la communauté.</p>
    </div>

    @if (session('status'))
        <p class="auth-status" role="status">{{ session('status') }}</p>
    @endif

    <form class="auth-form" method="POST" action="/login">
        @csrf
        <div class="auth-field">
            <label for="email">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" aria-describedby="@error('email') email-error @enderror">
            @error('email')<p class="auth-error" id="email-error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" aria-describedby="@error('password') password-error @enderror">
            @error('password')<p class="auth-error" id="password-error">{{ $message }}</p>@enderror
        </div>
        <label class="auth-check" for="remember_me">
            <input id="remember_me" name="remember" type="checkbox">
            <span>Se souvenir de moi</span>
        </label>
        <button class="auth-submit" type="submit">Se connecter <span aria-hidden="true">↗</span></button>
    </form>

    <div class="auth-links">
        @if (Route::has('password.request'))
            <a href="/forgot-password">Mot de passe oublié ?</a>
        @endif
        <p>Pas encore membre ? <a href="/register">Créer un compte</a></p>
    </div>
</x-guest-layout>
