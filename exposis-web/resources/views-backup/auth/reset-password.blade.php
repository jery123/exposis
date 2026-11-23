<x-guest-layout>
    <div class="auth-heading">
        <span class="auth-step">Accès au compte · Nouveau mot de passe</span>
        <h2>Choisissez un nouveau mot de passe.</h2>
        <p>Choisissez un mot de passe que vous n’utilisez pas déjà ailleurs.</p>
    </div>
    <form class="auth-form" method="POST" action="/reset-password">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="auth-field">
            <label for="email">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label for="password">Nouveau mot de passe</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">
            @error('password')<p class="auth-error">{{ $message }}</p>@enderror
        </div>
        <div class="auth-field">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
            @error('password_confirmation')<p class="auth-error">{{ $message }}</p>@enderror
        </div>
        <button class="auth-submit" type="submit">Enregistrer le nouveau mot de passe <span aria-hidden="true">↗</span></button>
    </form>
</x-guest-layout>
