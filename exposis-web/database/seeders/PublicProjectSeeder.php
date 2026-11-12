<?php

namespace Database\Seeders;

use App\Models\PublicProject;
use Illuminate\Database\Seeder;

class PublicProjectSeeder extends Seeder
{
    public function run(): void
    {
        PublicProject::query()
            ->where('slug', 'exemple-projet')
            ->where('is_sample', true)
            ->delete();

        $projects = [
            [
                'slug' => 'sentinelle-numerique-locale',
                'title' => 'Sentinelle numérique locale',
                'summary' => 'Exemple fictif de démarche pour sensibiliser une petite organisation aux risques de sécurité numérique.',
                'body' => <<<'MARKDOWN'
## Exemple de démonstration

Ce cas est entièrement fictif. Il illustre le type de documentation qu’un projet communautaire peut publier après validation.

## Contexte

Une petite association souhaite mieux protéger ses comptes, ses appareils et les informations de ses membres, sans disposer d’une équipe informatique dédiée.

## Démarche proposée

- Recenser les usages et les comptes essentiels avec l’accord de l’organisation.
- Préparer une grille de vérification non intrusive sur les mises à jour, les sauvegardes et l’authentification.
- Rédiger des recommandations accessibles et priorisées.

## Résultats attendus

Un guide pratique et une séance de sensibilisation. Aucun audit réel ni test d’intrusion n’a été réalisé dans le cadre de cet exemple.

## Limites

Cette fiche ne remplace pas un audit professionnel et ne décrit pas une organisation réelle.
MARKDOWN,
                'pole' => 'Cybersécurité',
                'status' => 'Prototype',
                'team_members' => ['Équipe Sentinelle (exemple fictif)'],
            ],
            [
                'slug' => 'assistant-documentaire-ivoirien',
                'title' => 'Assistant documentaire ivoirien',
                'summary' => 'Exemple fictif d’assistant de recherche qui aide à retrouver des informations dans un corpus documentaire autorisé.',
                'body' => <<<'MARKDOWN'
## Exemple de démonstration

Ce projet est fictif et sert à montrer comment documenter une expérimentation en intelligence artificielle.

## Contexte

Une équipe pédagogique veut explorer une interface qui facilite la consultation de supports de cours et de documents qu’elle est autorisée à utiliser.

## Démarche proposée

- Constituer un petit corpus de démonstration avec des documents libres ou créés pour le test.
- Comparer les réponses de l’assistant avec les passages sources retrouvés.
- Présenter les limites, les erreurs possibles et les règles de protection des données.

## Résultats attendus

Un prototype d’interface et un protocole d’évaluation documenté. Aucun modèle n’a été entraîné et aucun résultat de performance réel n’est revendiqué.

## Précautions

Les données personnelles et les documents soumis à restriction ne doivent pas être intégrés au corpus.
MARKDOWN,
                'pole' => 'Intelligence Artificielle',
                'status' => 'Prototype',
                'team_members' => ['Collectif Baobab (exemple fictif)'],
            ],
            [
                'slug' => 'carnet-de-terrain-ouvert',
                'title' => 'Carnet de terrain ouvert',
                'summary' => 'Exemple fictif d’outil libre pour organiser des observations de terrain et les exporter dans des formats réutilisables.',
                'body' => <<<'MARKDOWN'
## Exemple de démonstration

Ce cas fictif présente une idée de logiciel libre et la manière dont son développement pourrait être documenté.

## Besoin

Des équipes de recherche souhaitent consigner des observations structurées, même lorsque la connexion Internet est limitée, puis réutiliser leurs données.

## Fonctionnalités envisagées

- Créer des formulaires d’observation configurables.
- Conserver temporairement les entrées sur l’appareil.
- Exporter les données dans un format ouvert après validation par l’utilisateur.

## Étapes de réalisation proposées

Commencer par un prototype hors ligne, tester son ergonomie avec des données synthétiques, puis publier le code et la documentation sous une licence à définir.

## État

Il s’agit d’un scénario de démonstration, pas d’un produit existant ni d’un dépôt de code actif.
MARKDOWN,
                'pole' => 'Logiciel & Open Source',
                'status' => 'Recherche',
                'team_members' => ['Atelier Nimba (exemple fictif)'],
            ],
            [
                'slug' => 'observatoire-connectivite-communautaire',
                'title' => 'Observatoire de connectivité communautaire',
                'summary' => 'Exemple fictif de tableau de bord pour visualiser des mesures de connectivité agrégées et partagées avec consentement.',
                'body' => <<<'MARKDOWN'
## Exemple de démonstration

Cette fiche fictive illustre un projet de visualisation de données réseau sans collecte de données personnelles.

## Question étudiée

Comment représenter les variations de qualité de connexion signalées volontairement par des participants, tout en expliquant les limites des mesures ?

## Prototype envisagé

- Saisir des mesures de test avec une localisation volontairement approximative.
- Afficher des tendances agrégées par période.
- Documenter les conditions de mesure et les marges d’incertitude.

## Résultats attendus

Une maquette de tableau de bord et un protocole de collecte transparent. Aucune mesure réelle n’est présentée dans cet exemple.

## Protection des participants

La collecte réelle nécessiterait un consentement éclairé, une minimisation des données et une durée de conservation définie.
MARKDOWN,
                'pole' => 'Réseaux Cloud & Edge',
                'status' => 'Prototype',
                'team_members' => ['Groupe Horizon (exemple fictif)'],
            ],
            [
                'slug' => 'atelier-initiation-aux-donnees',
                'title' => 'Atelier d’initiation aux données',
                'summary' => 'Exemple fictif de parcours pédagogique pour découvrir les bases du nettoyage, de l’analyse et de la visualisation de données.',
                'body' => <<<'MARKDOWN'
## Exemple de démonstration

Ce scénario pédagogique est fictif et montre comment partager les objectifs et le déroulé d’une activité de formation.

## Objectifs

À la fin de l’atelier, les participants devraient pouvoir reconnaître des valeurs manquantes, produire quelques statistiques descriptives et expliquer un graphique simple.

## Déroulé proposé

1. Découvrir un jeu de données synthétique et son dictionnaire.
2. Repérer des incohérences et proposer des corrections.
3. Créer une visualisation et discuter de ce qu’elle permet — ou ne permet pas — de conclure.

## Ressources

Le matériel de démonstration serait publié dans des formats réutilisables, avec une licence claire et des consignes accessibles aux débutants.

## État

Cette fiche est un exemple fictif ; aucune session ni aucun résultat de formation réel n’est revendiqué.
MARKDOWN,
                'pole' => 'Formation',
                'status' => 'Terminé',
                'team_members' => ['Équipe Découverte (exemple fictif)'],
            ],
        ];

        foreach ($projects as $project) {
            PublicProject::updateOrCreate(
                ['slug' => $project['slug']],
                $project + [
                    'repo_url' => null,
                    'is_sample' => true,
                    'published_at' => now(),
                ]
            );
        }
    }
}
