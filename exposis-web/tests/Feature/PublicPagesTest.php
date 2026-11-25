<?php
namespace Tests\Feature;

use App\Models\PublicProject;
use App\Support\ReportDocumentRenderer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load(): void
    {
        foreach (['/', '/a-propos', '/services', '/projets', '/contact', '/mentions-legales'] as $u) $this->get($u)->assertOk();
    }

    public function test_only_published_projects_are_visible(): void
    {
        $base = ['title' => 'T', 'summary' => 'S', 'body' => '## Hi', 'pole' => 'Formation', 'status' => 'MVP'];
        PublicProject::create($base + ['slug' => 'live', 'published_at' => now()->subDay()]);
        PublicProject::create($base + ['slug' => 'draft', 'published_at' => null]);
        $this->get('/projets/live')->assertOk();
        $this->get('/projets/draft')->assertNotFound();
        $this->get('/projets?pole=Formation')->assertOk();
    }

    public function test_public_project_catalog_filters_by_search_term(): void
    {
        PublicProject::create([
            'title' => 'Projet collaboratif',
            'summary' => 'Une expérimentation menée ensemble.',
            'body' => 'Description du projet.',
            'pole' => 'Formation',
            'status' => 'Prototype',
            'slug' => 'projet-collaboratif',
            'published_at' => now()->subDay(),
            'team_members' => ['Awa Kouamé'],
        ]);
        PublicProject::create([
            'title' => 'Projet réseau',
            'summary' => 'Une cartographie collaborative.',
            'body' => 'Description du projet réseau.',
            'pole' => 'Réseaux Cloud & Edge',
            'status' => 'Prototype',
            'slug' => 'projet-reseau',
            'published_at' => now()->subDay(),
            'team_members' => ['Yao N’Guessan'],
        ]);

        $this->get('/projets?q=Awa')->assertOk()->assertSee('Projet collaboratif')->assertDontSee('Projet réseau');
    }

    public function test_public_project_catalog_paginates_results_and_keeps_the_total_count(): void
    {
        foreach (range(1, 10) as $index) {
            PublicProject::create([
                'title' => 'Projet catalogue '.$index,
                'summary' => 'Un projet publié dans le catalogue.',
                'body' => 'Description du projet.',
                'pole' => 'Formation',
                'status' => 'Terminé',
                'slug' => 'projet-catalogue-'.$index,
                'published_at' => now()->subSeconds(11 - $index),
            ]);
        }

        $this->get(route('public.projects.index'))
            ->assertOk()
            ->assertSee('10 projets')
            ->assertSee('page=2')
            ->assertSee('Projet catalogue 10');

        $this->get(route('public.projects.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Projet catalogue 1');
    }

    public function test_published_project_shows_its_participants(): void
    {
        $project = PublicProject::create([
            'title' => 'Projet collaboratif',
            'summary' => 'Une expérimentation menée ensemble.',
            'body' => 'Description du projet.',
            'pole' => 'Formation',
            'status' => 'Prototype',
            'slug' => 'projet-collaboratif',
            'published_at' => now()->subDay(),
            'team_members' => ['Awa Kouamé', 'Yao N’Guessan'],
        ]);

        $this->get(route('public.projects.show', $project))
            ->assertOk()
            ->assertSee('Celles et ceux qui le font avancer')
            ->assertSee('Awa Kouamé')
            ->assertSee('Yao N’Guessan');
    }

    public function test_visitors_can_only_view_the_paginated_preview_of_an_approved_report(): void
    {
        Storage::fake('local');
        $project = PublicProject::create([
            'title' => 'Projet avec rapport',
            'summary' => 'Une expérimentation documentée.',
            'body' => 'Description du projet.',
            'pole' => 'Formation',
            'status' => 'Terminé',
            'slug' => 'projet-avec-rapport',
            'published_at' => now()->subMinute(),
        ]);
        $report = $project->report()->create([
            'path' => 'community-projects/'.$project->id.'/reports/rapport.pdf',
            'original_name' => 'rapport.pdf',
            'approved_at' => now()->subMinute(),
        ]);
        Storage::disk('local')->put($report->path, 'private report bytes');

        $renderer = \Mockery::mock(ReportDocumentRenderer::class);
        $renderer->shouldReceive('previewPageCount')
            ->once()
            ->with(\Mockery::type(\App\Models\PublicProjectReport::class))
            ->andReturn(2);
        $renderer->shouldReceive('previewImage')
            ->once()
            ->with(\Mockery::type(\App\Models\PublicProjectReport::class), 1)
            ->andReturn('preview image bytes');
        $this->app->instance(ReportDocumentRenderer::class, $renderer);

        $this->get(route('public.projects.report.preview', $project))
            ->assertOk()
            ->assertSee('Aperçu limité · 2 pages')
            ->assertSee(route('public.projects.report.preview.page', [$project, 'page' => 1]))
            ->assertDontSee($report->path);

        $this->get(route('public.projects.report.preview.page', [$project, 'page' => 1]))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertSee('preview image bytes');

        $this->get(route('public.projects.report.viewer', $project))
            ->assertRedirect(route('login'));
    }

    public function test_visitors_cannot_preview_an_unapproved_project_report(): void
    {
        $project = PublicProject::create([
            'title' => 'Projet en attente',
            'summary' => 'Une expérimentation documentée.',
            'body' => 'Description du projet.',
            'pole' => 'Formation',
            'status' => 'Terminé',
            'slug' => 'projet-en-attente',
            'published_at' => now()->subMinute(),
        ]);
        $project->report()->create([
            'path' => 'community-projects/'.$project->id.'/reports/rapport.pdf',
            'original_name' => 'rapport.pdf',
        ]);

        $this->get(route('public.projects.report.preview', $project))->assertNotFound();
    }
}
