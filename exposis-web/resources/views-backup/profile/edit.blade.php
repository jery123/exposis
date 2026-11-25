<x-app-layout>
    <main class="profile-page">
        <div class="profile-page__container">
            <header class="profile-page__intro">
                <span class="profile-page__eyebrow"><span aria-hidden="true"></span> Espace membre · EXPOSIS</span>
                <h1>Votre profil,<br><em>à votre image.</em></h1>
                <p>Gérez vos informations et la sécurité de votre compte depuis un seul endroit.</p>
            </header>

            <section class="profile-page__identity" aria-label="Résumé du compte">
                <span class="profile-page__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                <div class="profile-page__identity-copy">
                    <span class="profile-page__eyebrow">Votre compte</span>
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="profile-page__identity-meta">
                    <span class="profile-page__member-badge"><span aria-hidden="true"></span>{{ $user->role === 'admin' ? 'Administrateur' : 'Membre' }}</span>
                    <span class="profile-page__joined">Membre depuis {{ $user->created_at->format('d/m/Y') }}</span>
                </div>
            </section>

            <div class="profile-page__sections">
                <section class="profile-page__card profile-page__card--information" aria-labelledby="profile-information-heading">
                    <div class="profile-page__card-heading">
                        <span class="profile-page__card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.5"/><path d="M5.5 19c.6-3.1 3-4.8 6.5-4.8s5.9 1.7 6.5 4.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </span>
                        <div>
                            <span class="profile-page__eyebrow">Informations personnelles</span>
                            <h2 id="profile-information-heading">Vos coordonnées</h2>
                            <p>Ces informations nous aident à vous reconnaître et à vous contacter.</p>
                        </div>
                    </div>
                    @include('profile.partials.update-profile-information-form')
                </section>

                <section class="profile-page__card profile-page__card--security" aria-labelledby="profile-security-heading">
                    <div class="profile-page__card-heading">
                        <span class="profile-page__card-icon profile-page__card-icon--security" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none"><rect x="5" y="10" width="14" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </span>
                        <div>
                            <span class="profile-page__eyebrow">Sécurité du compte</span>
                            <h2 id="profile-security-heading">Mot de passe</h2>
                            <p>Choisissez un mot de passe robuste et unique pour protéger votre compte.</p>
                        </div>
                    </div>
                    @include('profile.partials.update-password-form')
                </section>
            </div>

            <section class="profile-page__danger" aria-labelledby="profile-delete-heading">
                <div>
                    <span class="profile-page__eyebrow">Action irréversible</span>
                    <h2 id="profile-delete-heading">Supprimer le compte</h2>
                    <p>La suppression efface définitivement votre compte et les données associées.</p>
                </div>
                @include('profile.partials.delete-user-form')
            </section>
        </div>
    </main>
</x-app-layout>
