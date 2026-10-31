<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\PublicProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_lists_only_published_projects_and_supports_domain_filters(): void
    {
        $published = PublicProject::create([
            'title' => 'Projet de formation',
            'summary' => 'Une expérimentation pédagogique.',
            'body' => 'Description.',
            'pole' => 'Formation',
            'status' => 'Prototype',
            'slug' => 'projet-formation',
            'published_at' => now()->subDay(),
            'team_members' => ['Awa Kouamé'],
        ]);
        PublicProject::create([
            'title' => 'Projet privé',
            'summary' => 'Encore en revue.',
            'body' => 'Description.',
            'pole' => 'Formation',
            'status' => 'Prototype',
            'slug' => 'projet-prive',
            'published_at' => null,
        ]);

        $this->getJson('/api/public/projects')
            ->assertOk()
            ->assertJsonCount(1, 'projects')
            ->assertJsonPath('projects.0.slug', $published->slug)
            ->assertJsonMissingPath('projects.0.body')
            ->assertJsonPath('projects.0.team_members.0', 'Awa Kouamé');

        $this->getJson('/api/public/projects?pole=Cybersécurité')
            ->assertOk()
            ->assertJsonCount(0, 'projects');

        $this->getJson('/api/public/projects?q=Awa')
            ->assertOk()
            ->assertJsonCount(1, 'projects')
            ->assertJsonPath('projects.0.slug', $published->slug);

        $this->getJson('/api/public/projects?q=Kouamé')
            ->assertOk()
            ->assertJsonCount(1, 'projects')
            ->assertJsonPath('projects.0.slug', $published->slug);

        $this->getJson('/api/public/projects?q=introuvable')
            ->assertOk()
            ->assertJsonCount(0, 'projects');
    }

    public function test_api_project_detail_includes_description_and_hides_unpublished_projects(): void
    {
        PublicProject::create([
            'title' => 'Projet publié',
            'summary' => 'Résumé public.',
            'body' => "Premier paragraphe.\nDeuxième paragraphe.",
            'pole' => 'Formation',
            'status' => 'Prototype',
            'slug' => 'projet-publie',
            'published_at' => now()->subDay(),
            'team_members' => ['Awa Kouamé'],
        ]);
        PublicProject::create([
            'title' => 'Projet privé',
            'summary' => 'Encore en revue.',
            'body' => 'Description.',
            'pole' => 'Formation',
            'status' => 'Prototype',
            'slug' => 'projet-prive',
            'published_at' => null,
        ]);

        $this->getJson('/api/public/projects/projet-publie')
            ->assertOk()
            ->assertJsonPath('project.body', "Premier paragraphe.\nDeuxième paragraphe.")
            ->assertJsonPath('project.team_members.0', 'Awa Kouamé');

        $this->getJson('/api/public/projects/projet-prive')->assertNotFound();
    }

    public function test_api_accepts_a_visitor_submission_for_admin_review(): void
    {
        $this->postJson('/api/public/submissions', [
            'contact_name' => 'Awa Kouamé',
            'contact_email' => 'awa@example.ci',
            'title' => 'Outil numérique local',
            'request_type' => 'feasibility',
            'problem' => 'Un problème concret à résoudre.',
            'description' => 'Une proposition détaillée pour le laboratoire.',
            'consent' => '1',
            'website' => '',
        ])->assertCreated()->assertJsonStructure(['reference']);

        $project = Project::firstOrFail();
        $this->assertNull($project->user_id);
        $this->assertSame('awa@example.ci', $project->submitter_email);
        $this->assertSame('submitted', $project->status);
    }
}
