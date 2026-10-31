@extends('layouts.public')
@section('title', 'Soumettre un projet | EXPOSIS')
@section('description', 'Proposez une idée, un prototype ou une recherche à EXPOSIS pour une première étude de faisabilité.')
@section('content')
<div class="ph"><div class="w">
  <p class="eyb">Une idée à explorer ?</p>
  <h1>Parlons de votre projet.</h1>
  <p class="lead">Décrivez le problème et l’équipe EXPOSIS étudiera sa faisabilité. Aucun compte n’est nécessaire.</p>
</div></div>
<section><div class="w two">
  <div>
    <h2 class="h2">Une première étape, sans engagement.</h2>
    <p class="lead" style="margin-top:20px">Votre demande est lue par notre équipe. Nous revenons vers vous pour préciser les prochaines étapes avant tout travail approfondi.</p>
    <ul class="pri">
      <li><b>Votre idée reste la vôtre</b><span>La soumission ne transfère aucun droit de propriété intellectuelle.</span></li>
      <li><b>Évaluation par l’équipe</b><span>Nous examinons la faisabilité, les ressources et l’utilité du projet.</span></li>
      <li><b>Confidentialité respectée</b><span>Un NDA pourra être convenu avant le partage d’informations sensibles.</span></li>
    </ul>
  </div>
  <form id="f" method="POST" action="{{ route('public.submissions.store') }}">
    @csrf
    @php $f = 'w-full'; @endphp
    @if ($errors->any())
      <p class="form-alert" role="alert">Vérifiez les champs signalés puis renvoyez votre demande.</p>
    @endif
    <div>
      <label for="contact_name">Votre nom *</label>
      <input id="contact_name" name="contact_name" autocomplete="name" maxlength="255" value="{{ old('contact_name') }}" required>
      @error('contact_name')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="f2">
      <div>
        <label for="contact_email">Adresse e-mail *</label>
        <input id="contact_email" type="email" name="contact_email" autocomplete="email" maxlength="255" value="{{ old('contact_email') }}" required>
        @error('contact_email')<p class="form-error">{{ $message }}</p>@enderror
      </div>
      <div>
        <label for="contact_phone">Téléphone (facultatif)</label>
        <input id="contact_phone" type="tel" name="contact_phone" autocomplete="tel" maxlength="40" value="{{ old('contact_phone') }}">
        @error('contact_phone')<p class="form-error">{{ $message }}</p>@enderror
      </div>
    </div>
    <div>
      <label for="contact_organization">Organisation (facultatif)</label>
      <input id="contact_organization" name="contact_organization" autocomplete="organization" maxlength="255" value="{{ old('contact_organization') }}">
      @error('contact_organization')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
      <label for="title">Titre du projet *</label>
      <input id="title" name="title" maxlength="150" value="{{ old('title') }}" required>
      @error('title')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
      <label for="request_type">Type de demande *</label>
      <select id="request_type" name="request_type" required>
        <option value="">Choisir…</option>
        @foreach (\App\Models\Project::TYPES as $key => $label)
          <option value="{{ $key }}" @selected(old('request_type') === $key)>{{ $label }}</option>
        @endforeach
      </select>
      @error('request_type')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
      <label for="problem">Quel problème souhaitez-vous résoudre ? *</label>
      <textarea id="problem" name="problem" rows="4" maxlength="2000" required>{{ old('problem') }}</textarea>
      @error('problem')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div>
      <label for="description">Décrivez votre idée ou votre projet *</label>
      <textarea id="description" name="description" rows="6" maxlength="5000" required>{{ old('description') }}</textarea>
      @error('description')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="f2">
      <div>
        <label for="timeline">Délai souhaité</label>
        <input id="timeline" name="timeline" maxlength="150" value="{{ old('timeline') }}">
        @error('timeline')<p class="form-error">{{ $message }}</p>@enderror
      </div>
      <div>
        <label for="budget">Budget indicatif</label>
        <input id="budget" name="budget" maxlength="150" value="{{ old('budget') }}">
        @error('budget')<p class="form-error">{{ $message }}</p>@enderror
      </div>
    </div>
    <div class="hp" aria-hidden="true"><label for="website">Ne pas remplir</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div>
    <label class="chk"><input type="checkbox" name="confidential" value="1" @checked(old('confidential'))> Certaines informations de ce projet sont confidentielles.</label>
    <div>
      <label class="chk"><input type="checkbox" name="consent" value="1" @checked(old('consent')) required> J’accepte que mes coordonnées et les informations fournies soient utilisées par EXPOSIS pour traiter cette demande. *</label>
      @error('consent')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <button class="btn o" type="submit">Envoyer la demande</button>
    <p class="field-note">Les champs marqués d’un * sont obligatoires. Vous pouvez aussi <a href="{{ route('register') }}">créer un compte membre</a>.</p>
  </form>
</div></section>
@endsection
