@extends('layouts.public')
@section('title', 'Domaines et services | EXPOSIS')
@section('description', 'Étude de faisabilité, prototype MVP, collaboration de recherche et formation avec EXPOSIS.')
@section('content')
    <section class="portal">
        <div class="portal__inner">
            <header class="portal__heading"><span class="portal__eyebrow">EXPOSIS · Services</span><h1>De l’idée au prototype.</h1><p>Quatre façons de travailler avec le laboratoire.</p></header>
            <div class="portal__grid">
                <article class="portal__card"><h2>Étude de faisabilité</h2><p>Évaluer les contraintes techniques, les risques et les pistes de mise en œuvre.</p></article>
                <article class="portal__card"><h2>Prototype MVP</h2><p>Construire et tester une première version utile de votre solution.</p></article>
                <article class="portal__card"><h2>Collaboration de recherche</h2><p>Explorer une question avec une démarche expérimentale et documentée.</p></article>
                <article class="portal__card"><h2>Formation</h2><p>Développer les compétences numériques par la pratique et le partage.</p></article>
            </div>
            <section class="portal__card" style="margin-top:1rem">
                <h2>Comment ça marche</h2>
                <ol><li>Vous décrivez votre idée.</li><li>Nous évaluons sa faisabilité.</li><li>Nous construisons un prototype.</li><li>Nous partageons les résultats.</li></ol>
                <p>Soumettre un projet ne transfère aucun droit. Un accord ou un NDA précède tout travail approfondi.</p>
                <h2>Questions fréquentes</h2>
                <details><summary>Est-ce que je perds des droits sur mon idée ?</summary><p>Non. Soumettre un projet ne transfère aucun droit de propriété intellectuelle.</p></details>
                <details><summary>Y a-t-il un accord de confidentialité ?</summary><p>Oui. Un accord ou un NDA précède tout travail approfondi. Vous pouvez aussi indiquer que votre projet est confidentiel.</p></details>
                <details><summary>Quels types de projets acceptez-vous ?</summary><p>Études de faisabilité, prototypes MVP et collaborations de recherche en cybersécurité, intelligence artificielle, logiciel et open source, réseaux, cloud et edge, et formation.</p></details>
                <details><summary>Que se passe-t-il après ma demande ?</summary><p>Nous la lisons et vous répondons par e-mail. Précisez votre délai et votre budget indicatif : cela nous aide à cadrer la suite.</p></details>
                <a class="portal__button portal__button--accent" href="{{ route('public.submissions.create') }}">Soumettre un projet</a>
            </section>
        </div>
    </section>
@endsection
