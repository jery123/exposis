@extends('layouts.public')
@section('title', 'À propos | EXPOSIS, laboratoire de recherche informatique')
@section('description', 'EXPOSIS est une organisation à but non lucratif, indépendante, qui fait progresser les compétences et l’innovation informatique en Côte d’Ivoire.')
@section('content')
  <section class="about-hero" aria-labelledby="about-title">
    <div class="w"><h1 id="about-title">À propos</h1></div>
  </section>

  <section class="about-intro" aria-labelledby="about-intro-title">
    <div class="w">
      <div class="about-copy">
        <h2 id="about-intro-title">Le laboratoire EXPOSIS</h2>
        <p>EXPOSIS est le Laboratoire de Recherche, d’Expérimentation et d’Innovation en Informatique. Organisation à but non lucratif, indépendante de toute affiliation politique ou religieuse, nous contribuons au développement des compétences et à l’innovation informatique en Côte d’Ivoire et en Afrique.</p>
        <p>Par la recherche appliquée, l’expérimentation et la diffusion de solutions numériques, nous apprenons en faisant, construisons des réponses utiles et partageons les connaissances acquises.</p>
      </div>
    </div>
  </section>

  <section class="about-team" id="equipe" aria-labelledby="team-title">
    <div class="w">
      <div class="about-team-heading">
        <h2 id="team-title">Notre équipe</h2>
        <p>Les personnes qui font vivre la recherche, l’expérimentation et l’innovation chez EXPOSIS.</p>
      </div>
      <div class="about-team-grid">
        <article class="about-member">
          <div class="about-member-top">
            <div class="about-member-portrait" aria-hidden="true">EX</div>
            <div>
              <h3>Nom du membre</h3>
              <p class="about-member-role">Intitulé du poste</p>
            </div>
          </div>
          <p class="about-member-bio">Les noms, fonctions et portraits seront ajoutés dès réception des informations de l’équipe.</p>
        </article>
      </div>
    </div>
  </section>
@endsection
