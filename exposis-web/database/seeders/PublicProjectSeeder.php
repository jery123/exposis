<?php
namespace Database\Seeders;

use App\Models\PublicProject;
use Illuminate\Database\Seeder;

class PublicProjectSeeder extends Seeder
{
    public function run(): void
    {
        PublicProject::updateOrCreate(['slug' => 'exemple-projet'], [
            'title' => 'Exemple de projet (fiche modèle)',
            'summary' => 'Fiche d’exemple montrant comment un projet EXPOSIS est présenté. À remplacer par un vrai projet.',
            'body' => "## Contexte\n\nÀ remplacer par le problème réel étudié.\n\n## Démarche\n\nÀ remplacer par la méthode, les outils et les choix techniques.\n\n## Résultats\n\nÀ remplacer par les résultats, la documentation et le lien vers le code ouvert.",
            'pole' => 'Logiciel & Open Source', 'status' => 'Prototype', 'is_sample' => true, 'published_at' => now(),
        ]);
    }
}
