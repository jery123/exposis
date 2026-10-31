<?php
namespace Tests\Feature;

use App\Models\PublicProject;
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
            ->assertSee('L’équipe du projet')
            ->assertSee('Awa Kouamé')
            ->assertSee('Yao N’Guessan');
    }
}
