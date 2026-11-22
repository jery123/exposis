@extends('layouts.public')
@section('title', 'Choisir un nouveau mot de passe | EXPOSIS')
@section('content')
    <section class="portal"><div class="portal__inner">
        <header class="portal__heading"><span class="portal__eyebrow">Espace membre</span><h1>Choisir un nouveau mot de passe.</h1></header>
        @if ($errors->any())<div class="portal-message portal-message--error" role="alert"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form action="{{ route('password.store') }}" class="portal__form" method="POST">
            @csrf
            <input name="token" type="hidden" value="{{ $request->route('token') }}">
            <label class="portal__field"><span class="portal__label">Adresse e-mail</span><input autocomplete="email" name="email" required type="email" value="{{ old('email', $request->email) }}">@error('email')<span class="portal__error">{{ $message }}</span>@enderror</label>
            <label class="portal__field"><span class="portal__label">Nouveau mot de passe</span><input autocomplete="new-password" name="password" required type="password">@error('password')<span class="portal__error">{{ $message }}</span>@enderror</label>
            <label class="portal__field"><span class="portal__label">Confirmer le mot de passe</span><input autocomplete="new-password" name="password_confirmation" required type="password"></label>
            <button class="portal__button portal__button--accent" type="submit">Réinitialiser le mot de passe</button>
        </form>
    </div></section>
@endsection
