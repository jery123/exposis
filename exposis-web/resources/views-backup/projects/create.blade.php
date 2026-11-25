<x-app-layout>
  <main class="project-submit-page">
    <div class="project-submit-page__container">
      <header class="project-submit-page__hero">
        <span class="project-submit-page__eyebrow"><span aria-hidden="true"></span> Espace membre · EXPOSIS</span>
        <h1>Une idée prend<br><em>forme ici.</em></h1>
        <p>Décrivez le défi que vous souhaitez relever. L’équipe EXPOSIS étudiera votre demande et vous accompagnera pour définir les prochaines étapes.</p>
      </header>

      <div class="project-submit-page__layout">
        <aside class="project-submit-page__aside">
          <span class="project-submit-page__aside-label">Après votre envoi</span>
          <ol>
            <li><span>01</span><div><strong>Nous prenons connaissance</strong><p>Votre demande est lue par l’équipe EXPOSIS.</p></div></li>
            <li><span>02</span><div><strong>Nous étudions la faisabilité</strong><p>Nous examinons le besoin, les ressources et les prochaines étapes.</p></div></li>
            <li><span>03</span><div><strong>Nous échangeons avec vous</strong><p>Suivez les réponses et les échanges depuis votre espace membre.</p></div></li>
          </ol>
          <div class="project-submit-page__note">
            <span aria-hidden="true">∞</span>
            <p>Votre idée reste la vôtre. La soumission ne transfère aucun droit de propriété intellectuelle. Un NDA précède tout travail approfondi.</p>
          </div>
        </aside>

        <form method="POST" action="{{ route('projects.store') }}" class="project-submit-form">
          @csrf
          <div class="project-submit-form__heading">
            <span>Votre demande</span>
            <h2>Parlons de votre projet</h2>
            <p>Les champs marqués d’un * sont obligatoires.</p>
          </div>

          <div class="project-submit-form__section">
            <span>01</span><h3>Le projet</h3>
          </div>
          <div class="project-submit-form__field">
            <x-input-label for="title" value="Titre du projet *"/>
            <input id="title" name="title" maxlength="150" value="{{ old('title') }}" required>
            <x-input-error :messages="$errors->get('title')"/>
          </div>
          <div class="project-submit-form__field">
            <x-input-label for="request_type" value="Type de demande *"/>
            <select id="request_type" name="request_type" required>
              <option value="">Choisir un type de projet…</option>
              @foreach (\App\Models\Project::TYPES as $k => $l)
                <option value="{{ $k }}" @selected(old('request_type') === $k)>{{ $l }}</option>
              @endforeach
            </select>
            <x-input-error :messages="$errors->get('request_type')"/>
          </div>
          <div class="project-submit-form__field">
            <x-input-label for="problem" value="Problème à résoudre *"/>
            <textarea id="problem" name="problem" rows="4" maxlength="2000" required>{{ old('problem') }}</textarea>
            <span class="project-submit-form__hint">Décrivez la situation ou le besoin auquel vous souhaitez répondre. (2 000 caractères maximum)</span>
            <x-input-error :messages="$errors->get('problem')"/>
          </div>
          <div class="project-submit-form__field">
            <x-input-label for="description" value="Description du projet *"/>
            <textarea id="description" name="description" rows="7" maxlength="5000" required>{{ old('description') }}</textarea>
            <span class="project-submit-form__hint">Votre idée, les personnes concernées et le résultat recherché. (5 000 caractères maximum)</span>
            <x-input-error :messages="$errors->get('description')"/>
          </div>

          <div class="project-submit-form__section">
            <span>02</span><h3>Calendrier et ressources</h3>
          </div>
          <div class="project-submit-form__two-fields">
            <div class="project-submit-form__field">
              <x-input-label for="timeline" value="Délai souhaité"/>
              <input id="timeline" name="timeline" maxlength="150" value="{{ old('timeline') }}" placeholder="Ex. 3 à 6 mois">
              <x-input-error :messages="$errors->get('timeline')"/>
            </div>
            <div class="project-submit-form__field">
              <x-input-label for="budget" value="Budget indicatif"/>
              <input id="budget" name="budget" maxlength="150" value="{{ old('budget') }}" placeholder="Ex. à définir">
              <x-input-error :messages="$errors->get('budget')"/>
            </div>
          </div>

          <div class="project-submit-page__honeypot" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
          <label class="project-submit-form__check"><input type="checkbox" name="confidential" value="1" @checked(old('confidential'))> <span>Les informations de ce projet sont confidentielles.</span></label>
          <div>
            <label class="project-submit-form__check"><input type="checkbox" name="consent" value="1" @checked(old('consent')) required> <span>J’accepte que ces données soient traitées pour répondre à ma demande. *</span></label>
            <x-input-error :messages="$errors->get('consent')"/>
          </div>
          <button class="project-submit-form__submit">Envoyer mon projet <span aria-hidden="true">↗</span></button>
          <p class="project-submit-form__privacy">Vos coordonnées sont utilisées uniquement pour le suivi de votre demande.</p>
        </form>
      </div>
    </div>
  </main>
</x-app-layout>
