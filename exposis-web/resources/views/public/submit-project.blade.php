@extends('layouts.public')
@section('title', 'Soumettre un projet | EXPOSIS')
@section('description', 'Proposez une idée à l’équipe EXPOSIS pour une première étude de faisabilité.')
@section('robots', 'noindex, follow')
@section('content')
    <section class="page-hero submit-hero">
        <div class="section-kicker"><span>Une idée à explorer ?</span> Première étape, sans engagement</div>
        <h1>Tout commence par<br><em>une bonne question.</em></h1>
        <p>Parlez-nous du problème que vous souhaitez résoudre. Notre équipe étudiera la faisabilité et reviendra vers vous.</p>
    </section>
    <section class="submission-section section-wrap">
        <aside class="submission-aside">
            <div class="section-kicker"><span>Le parcours</span> Après votre envoi</div>
            <ol class="submission-steps">
                <li><span>01</span><div><strong>On vous lit</strong><p>L’équipe prend connaissance de votre besoin et de son contexte.</p></div></li>
                <li><span>02</span><div><strong>On évalue</strong><p>Nous examinons la faisabilité, les ressources et l’utilité du projet.</p></div></li>
                <li><span>03</span><div><strong>On échange</strong><p>Nous vous recontactons pour clarifier les prochaines étapes.</p></div></li>
            </ol>
            <div class="privacy-note"><span class="privacy-infinity" aria-hidden="true">∞</span><p>Votre idée reste la vôtre. La soumission ne transfère aucun droit de propriété intellectuelle. Un NDA peut être convenu avant tout travail approfondi.</p></div>
            <p class="member-prompt">Déjà membre ? <a href="{{ route('community.projects.create') }}">Présentez un projet de la communauté ↗</a></p>
        </aside>
        <form action="{{ route('public.submissions.store') }}" class="submission-form" method="POST">
            @csrf
            <div class="submission-form-heading"><span class="section-kicker"><span>Votre proposition</span> Première étape</span><h2>Présentez votre projet</h2><p class="form-required">Les champs marqués d’un * sont obligatoires. Votre idée reste la vôtre et sera étudiée par notre équipe.</p></div>
            @if ($errors->any())<p class="form-alert" role="alert">Vérifiez les champs signalés puis renvoyez votre demande.</p>@endif
            <div class="form-grid">
                <div class="form-section-label form-full"><span>01</span><strong>Vous contacter</strong></div>
                <label class="form-field form-full" for="contact_name">Votre nom *
                    <input autocomplete="name" id="contact_name" maxlength="255" name="contact_name" required value="{{ old('contact_name') }}">
                    @error('contact_name')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field" for="contact_email">Adresse e-mail *
                    <input autocomplete="email" id="contact_email" maxlength="255" name="contact_email" required type="email" value="{{ old('contact_email') }}">
                    @error('contact_email')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field" for="contact_phone">Téléphone
                    <input autocomplete="tel" id="contact_phone" maxlength="40" name="contact_phone" type="tel" value="{{ old('contact_phone') }}">
                    @error('contact_phone')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field form-full" for="contact_organization">Organisation <span class="optional">(facultatif)</span>
                    <input autocomplete="organization" id="contact_organization" maxlength="255" name="contact_organization" value="{{ old('contact_organization') }}">
                    @error('contact_organization')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <div class="form-section-label form-full"><span>02</span><strong>Le projet</strong></div>
                <label class="form-field form-full" for="title">Titre du projet *
                    <input id="title" maxlength="150" name="title" required value="{{ old('title') }}">
                    @error('title')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field form-full" for="request_type">Type de demande *
                    <select id="request_type" name="request_type" required>
                        <option disabled value="" @selected(! old('request_type'))>Choisir un type de projet</option>
                        @foreach (\App\Models\Project::TYPES as $key => $label)<option value="{{ $key }}" @selected(old('request_type') === $key)>{{ $label }}</option>@endforeach
                    </select>
                    @error('request_type')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field form-full" for="problem">Quel problème souhaitez-vous résoudre ? *
                    <textarea id="problem" maxlength="2000" name="problem" required rows="3">{{ old('problem') }}</textarea>
                    @error('problem')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field form-full" for="description">Décrivez votre idée ou votre projet *
                    <textarea id="description" maxlength="5000" name="description" required rows="5">{{ old('description') }}</textarea>
                    @error('description')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field" for="timeline">Délai souhaité
                    <input id="timeline" maxlength="150" name="timeline" placeholder="Ex. 3 à 6 mois" value="{{ old('timeline') }}">
                    @error('timeline')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label class="form-field" for="budget">Budget indicatif
                    <input id="budget" maxlength="150" name="budget" placeholder="Ex. à définir" value="{{ old('budget') }}">
                    @error('budget')<span class="field-error">{{ $message }}</span>@enderror
                </label>
            </div>
            <div aria-hidden="true" class="honeypot"><label for="website">Ne pas remplir</label><input autocomplete="off" id="website" name="website" tabindex="-1"></div>
            <label class="check-field"><input name="confidential" type="checkbox" value="1" @checked(old('confidential'))> Certaines informations de ce projet sont confidentielles.</label>
            <label class="check-field check-consent"><input name="consent" required type="checkbox" value="1" @checked(old('consent'))> J’accepte que mes coordonnées et les informations fournies soient utilisées par EXPOSIS pour traiter cette demande. *</label>
            @error('consent')<p class="field-error" role="alert">{{ $message }}</p>@enderror
            <button class="button button-orange submit-button" type="submit">Envoyer la demande <span aria-hidden="true">↗</span></button>
            <p class="privacy-footnote">Vos coordonnées sont utilisées uniquement pour le suivi de votre demande.</p>
        </form>
    </section>
@endsection
