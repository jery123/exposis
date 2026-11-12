<x-app-layout>
  <main class="project-submit-page">
    <div class="project-submit-page__container">
      <header class="project-submit-page__hero">
        <span class="project-submit-page__eyebrow"><span aria-hidden="true"></span> Espace membre · Communauté</span>
        <h1>Votre travail mérite<br><em>d’être partagé.</em></h1>
        <p>Documentez un projet mené par la communauté. Après validation du projet et de son rapport, chacun pourra le découvrir et consulter librement sa documentation.</p>
      </header>

      <div class="project-submit-page__layout">
        <aside class="project-submit-page__aside">
          <span class="project-submit-page__aside-label">Du dépôt au partage</span>
          <ol>
            <li><span>01</span><div><strong>Documentez votre projet</strong><p>Décrivez l’objectif, la démarche et les résultats obtenus.</p></div></li>
            <li><span>02</span><div><strong>Ajoutez le rapport</strong><p>Joignez un rapport PDF ou DOCX pour partager le travail réalisé.</p></div></li>
            <li><span>03</span><div><strong>Attendez la validation</strong><p>Le projet et le rapport restent privés pendant leur examen.</p></div></li>
            <li><span>04</span><div><strong>Ouvrez l’accès à tous</strong><p>Une fois validés, le projet et son rapport sont accessibles aux visiteurs.</p></div></li>
          </ol>
          <div class="project-submit-page__note">
            <span aria-hidden="true">∞</span>
            <p>Vous devez avoir l’accord des participants et le droit de diffuser le contenu. Ne joignez aucun document confidentiel ou personnel.</p>
          </div>
        </aside>

        <form method="POST" enctype="multipart/form-data" action="{{ route('community.projects.store') }}" class="project-submit-form">
          @csrf
          <div class="project-submit-form__heading">
            <span>Votre contribution</span>
            <h2>Documentez le projet</h2>
            <p>Les champs marqués d’un * sont obligatoires.</p>
          </div>

          <div class="project-submit-form__section"><span>01</span><h3>Le projet</h3></div>
          <div class="project-submit-form__field">
            <x-input-label for="title" value="Titre du projet *"/>
            <input id="title" name="title" maxlength="150" value="{{ old('title') }}" required>
            <x-input-error :messages="$errors->get('title')"/>
          </div>
          <div class="project-submit-form__two-fields">
            <div class="project-submit-form__field">
              <x-input-label for="pole" value="Domaine *"/>
              <select id="pole" name="pole" required>
                <option value="">Choisir un domaine…</option>
                @foreach (\App\Models\PublicProject::POLES as $pole)
                  <option value="{{ $pole }}" @selected(old('pole') === $pole)>{{ $pole }}</option>
                @endforeach
              </select>
              <x-input-error :messages="$errors->get('pole')"/>
            </div>
            <div class="project-submit-form__field">
              <x-input-label for="status" value="Avancement *"/>
              <select id="status" name="status" required>
                @foreach (['Recherche', 'Prototype', 'MVP', 'Terminé'] as $status)
                  <option value="{{ $status }}" @selected(old('status', 'Prototype') === $status)>{{ $status }}</option>
                @endforeach
              </select>
              <x-input-error :messages="$errors->get('status')"/>
            </div>
          </div>
          <div class="project-submit-form__field">
            <x-input-label for="summary" value="Résumé *"/>
            <textarea id="summary" name="summary" rows="3" maxlength="400" required>{{ old('summary') }}</textarea>
            <span class="project-submit-form__hint">Présentez le projet en quelques phrases. (400 caractères maximum)</span>
            <x-input-error :messages="$errors->get('summary')"/>
          </div>
          <div class="project-submit-form__field">
            <x-input-label for="body" value="Description du projet *"/>
            <textarea id="body" name="body" rows="7" maxlength="50000" required>{{ old('body') }}</textarea>
            <span class="project-submit-form__hint">Expliquez le contexte, la démarche, les résultats ou les prochaines étapes.</span>
            <x-input-error :messages="$errors->get('body')"/>
          </div>

          <div class="project-submit-form__section"><span>02</span><h3>Équipe et ressources</h3></div>
          <div class="project-submit-form__field">
            <x-input-label for="team_members" value="Participants"/>
            <textarea id="team_members" name="team_members" rows="3" maxlength="2000" placeholder="Un nom par ligne">{{ old('team_members') }}</textarea>
            <span class="project-submit-form__hint">Jusqu’à 20 noms. Ajoutez uniquement les personnes ayant accepté leur mention.</span>
            <x-input-error :messages="$errors->get('team_members')"/>
          </div>
          <div class="project-submit-form__field">
            <x-input-label for="repo_url" value="Lien du projet (facultatif)"/>
            <input id="repo_url" type="url" name="repo_url" value="{{ old('repo_url') }}" placeholder="https://…">
            <span class="project-submit-form__hint">Site, démonstration, dépôt ou documentation.</span>
            <x-input-error :messages="$errors->get('repo_url')"/>
          </div>

          <div class="project-submit-form__section"><span>03</span><h3>Documents du projet</h3></div>
          <div class="project-submit-form__field">
            <x-input-label for="images" value="Images du projet (facultatif)"/>
            <input id="images" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
            <span class="project-submit-form__hint">JPG, PNG ou WebP · 5 images maximum · 5 Mo par image. Elles restent privées pendant la validation.</span>
            <x-input-error :messages="$errors->get('images')"/>
            @foreach ($errors->get('images.*') as $messages)
              <x-input-error :messages="$messages"/>
            @endforeach
          </div>
          <div class="project-submit-form__field project-submit-form__upload">
            <x-input-label for="report" value="Rapport du projet (facultatif)"/>
            <input id="report" type="file" name="report" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
            <span class="project-submit-form__hint">PDF ou DOCX · 20 Mo maximum. Le rapport reste privé jusqu’à sa validation distincte par EXPOSIS.</span>
            <x-input-error :messages="$errors->get('report')"/>
          </div>

          <label class="project-submit-form__check"><input type="checkbox" name="participant_consent" value="1" @checked(old('participant_consent')) required> <span>J’ai l’accord des participants et le droit de partager ce projet et son rapport publiquement après validation. Aucun renseignement confidentiel n’y figure. *</span></label>
          <x-input-error :messages="$errors->get('participant_consent')"/>
          <button class="project-submit-form__submit">Envoyer pour validation <span aria-hidden="true">↗</span></button>
          <p class="project-submit-form__privacy">Le projet, ses images et son rapport restent privés pendant l’examen. Un rapport remplacé doit être validé à nouveau avant toute diffusion publique.</p>
        </form>
      </div>
    </div>
  </main>
</x-app-layout>
