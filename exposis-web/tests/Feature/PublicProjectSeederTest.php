<?php

namespace Tests\Feature;

use App\Models\PublicProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProjectSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_creates_idempotent_published_sample_projects_for_every_domain(): void
    {
        PublicProject::create([
            'slug' => 'exemple-projet',
            'title' => 'Ancienne fiche modèle',
            'summary' => 'Ancienne fiche fictive.',
            'body' => 'Ancienne fiche fictive.',
            'pole' => 'Formation',
            'status' => 'Prototype',
            'is_sample' => true,
            'published_at' => now(),
        ]);

        $this->seed();
        $this->seed();

        $projects = PublicProject::query()->get();

        $this->assertCount(5, $projects);
        $this->assertTrue($projects->every(fn (PublicProject $project) => $project->is_sample && $project->published_at !== null));
        $this->assertEqualsCanonicalizing(PublicProject::POLES, $projects->pluck('pole')->unique()->all());
        $this->assertTrue($projects->every(fn (PublicProject $project) => str_contains($project->summary, 'fictif')));

        $this->getJson('/api/public/projects')
            ->assertOk()
            ->assertJsonCount(5, 'projects');
    }
}
