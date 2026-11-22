@extends('layouts.public')
@section('title', 'À propos | EXPOSIS')
@section('description', 'Découvrez EXPOSIS, sa mission et l’équipe qui fait avancer la recherche, l’expérimentation et l’innovation informatique en Côte d’Ivoire.')
@section('content')
    <div class="about-page">
        <section class="about-hero" aria-labelledby="about-title"><div class="about-hero-inner"><h1 id="about-title">À propos</h1></div></section>
        <section class="about-mission" aria-labelledby="about-intro-title">
            <div class="about-inner">
                <h2 id="about-intro-title">Le laboratoire EXPOSIS</h2>
                <p>EXPOSIS est le Laboratoire de Recherche, d’Expérimentation et d’Innovation en Informatique. Organisation indépendante de toute affiliation politique ou religieuse, nous contribuons au développement des compétences et à l’innovation informatique en Côte d’Ivoire et en Afrique.</p>
                <p>Par la recherche appliquée, l’expérimentation et la diffusion de solutions numériques, nous apprenons en faisant, construisons des réponses utiles et partageons les connaissances acquises.</p>
            </div>
        </section>
        <section class="about-team" id="equipe" aria-labelledby="team-title">
            <div class="about-inner">
                <div class="about-team-heading"><h2 id="team-title">Notre équipe</h2><p>Les personnes qui font vivre la recherche, l’expérimentation et l’innovation chez EXPOSIS.</p></div>
                <div class="about-team-grid">
                    <article class="about-member">
                        <div class="about-member-top">
                            <img class="about-portrait" src="{{ asset('images/team/1.jpeg') }}" alt="Portrait de Issouf D. Dayo" loading="lazy">
                            <div><h3>Issouf D. Dayo</h3><p class="about-member-role">Président | INP-HB | IT engineer | IT specialist</p>
                                {{-- <a href="mailto:jean.kouassi@exposis.net">jean.kouassi@exposis.net</a> --}}
                            </div>
                        </div>
                        {{-- <p class="about-member-bio">Jean KOUASSI est le Président du laboratoire EXPOSIS. Il est passionné par la recherche et l’innovation en informatique.</p> --}}
                    </article>
                    <article class="about-member">
                        <div class="about-member-top">
                            <img class="about-portrait" src="{{ asset('images/team/2.png') }}" alt="Portrait de IVES TAHOU" loading="lazy">
                            <div><h3>IVES TAHOU</h3><p class="about-member-role">Élève en fin de cycle ingénieur en télécommunications et réseaux.</p></div>
                        </div>
                    </article>
                    <article class="about-member">
                        <div class="about-member-top">
                            <img class="about-portrait" src="{{ asset('images/team/3.jpeg') }}" alt="Portrait de Jery Foto" loading="lazy">
                            <div><h3>Jery Foto</h3><p class="about-member-role">Network Consultant Intern | Computer Engineer | Cloud &amp; Cybersecurity | DevOps | AI/IoT Research Enthusiast</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </div>
@endsection
