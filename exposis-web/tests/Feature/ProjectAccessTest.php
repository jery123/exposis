<?php
namespace Tests\Feature;

use App\Models\Project;
use App\Models\PublicProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectAccessTest extends TestCase
{
    use RefreshDatabase;

    private function fakePng(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAFklEQVR4nGN8miDBwMDAxMDAwMDAAAARUwFhJHFLyAAAAABJRU5ErkJggg==', true)
        );
    }

    private function payload(array $o = []): array
    {
        return $o + ['title' => 'Test', 'request_type' => 'feasibility', 'problem' => 'Un problème assez long.', 'description' => 'Une description assez longue.', 'consent' => '1'];
    }

    public function test_guest_is_redirected(): void { $this->get('/projects')->assertRedirect('/login'); }

    public function test_member_projects_dashboard_shows_an_empty_state(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('projects.index'))
            ->assertOk()
            ->assertSee('Toutes vos demandes')
            ->assertSee('Votre prochain projet commence ici.')
            ->assertSee('Soumettre mon premier projet')
            ->assertSee('Partager un projet terminé');
    }

    public function test_member_projects_dashboard_shows_project_statuses_and_summary_counts(): void
    {
        $user = User::factory()->create();
        $inReview = $user->projects()->create($this->payload(['title' => 'Prototype agricole']));
        $inReview->status = 'in_review';
        $inReview->save();

        $resolved = $user->projects()->create($this->payload(['title' => 'Étude logistique']));
        $resolved->status = 'accepted';
        $resolved->save();

        $this->actingAs($user)
            ->get(route('projects.index'))
            ->assertOk()
            ->assertSee('Demandes envoyées')
            ->assertSee('En cours d’examen')
            ->assertSee('Réponses reçues')
            ->assertSee('2 projets')
            ->assertSee('Prototype agricole')
            ->assertSee('Étude logistique')
            ->assertSee('Accepté');
    }

    public function test_member_project_request_form_uses_the_submission_layout(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('projects.create'))
            ->assertOk()
            ->assertSee('Une idée prend')
            ->assertSee('Parlons de votre projet')
            ->assertSee('Calendrier et ressources')
            ->assertSee('name="consent"', false);
    }

    public function test_org_cannot_see_another_orgs_project(): void
    {
        [$a, $b] = User::factory()->count(2)->create();
        $p = $a->projects()->create($this->payload(['consent' => null]));
        $this->actingAs($b)->get(route('projects.show', $p))->assertForbidden();
        $this->actingAs($a)->get(route('projects.show', $p))->assertOk();
    }

    public function test_org_cannot_reach_admin(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/projects')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin/projects')->assertOk();
    }

    public function test_honeypot_blocks_submission(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u)->post('/projects', $this->payload(['website' => 'spam']))->assertSessionHasErrors('website');
        $this->assertSame(0, Project::count());
    }

    public function test_valid_submission_is_stored(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u)->post('/projects', $this->payload())->assertRedirect();
        $this->assertSame(1, $u->projects()->count());
    }

    public function test_public_visitor_can_submit_a_project_without_an_account(): void
    {
        $this->get(route('public.submissions.create'))
            ->assertOk()
            ->assertSee('Aucun compte n’est nécessaire.');

        $this->post(route('public.submissions.store'), $this->payload([
            'title' => 'Projet test',
            'contact_name' => 'Amadou Traoré',
            'contact_email' => 'amadou@example.ci',
            'contact_organization' => 'Collectif local',
            'consent' => '1',
        ]))->assertRedirect(route('public.submissions.received'));

        $project = Project::firstOrFail();
        $this->assertNull($project->user_id);
        $this->assertSame('amadou@example.ci', $project->submitter_email);
        $this->get(route('public.submissions.received'))
            ->assertOk()
            ->assertSee($project->reference);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Projet test');
        $this->actingAs($admin)->get(route('admin.projects.export'))->assertOk();
    }

    public function test_member_project_waits_for_admin_publication_and_keeps_participants(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->post(route('community.projects.store'), [
            'title' => 'Capteur agricole connecté',
            'pole' => 'Logiciel & Open Source',
            'status' => 'Prototype',
            'summary' => 'Un prototype pour suivre les conditions des cultures.',
            'body' => 'Le projet expérimente des capteurs adaptés au terrain.',
            'team_members' => "Awa Kouamé\nYao N’Guessan",
            'participant_consent' => '1',
        ])->assertRedirect(route('community.projects.index'));

        $project = PublicProject::firstOrFail();
        $this->assertSame($member->id, $project->user_id);
        $this->assertNull($project->published_at);
        $this->assertSame(['Awa Kouamé', 'Yao N’Guessan'], $project->team_members);
        $this->get(route('public.projects.show', $project))->assertNotFound();
        $this->actingAs($member)->get(route('community.projects.index'))
            ->assertOk()
            ->assertSee('En cours de validation · Voir mon projet')
            ->assertSee(route('community.projects.show', $project));
        $this->actingAs($member)->get(route('community.projects.show', $project))
            ->assertOk()
            ->assertSee('Capteur agricole connecté')
            ->assertSee('Ce projet reste privé');

        $this->actingAs(User::factory()->create())
            ->get(route('community.projects.show', $project))
            ->assertForbidden();
    }

    public function test_member_project_form_supports_completed_projects_and_images(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('community.projects.create'))
            ->assertOk()
            ->assertSee('Terminé')
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="images[]"', false)
            ->assertSee('Lien du projet');
    }

    public function test_guest_cannot_access_the_member_session_endpoint(): void
    {
        $this->getJson('/api/community/session')->assertUnauthorized();
    }

    public function test_member_can_submit_a_project_through_the_json_endpoint(): void
    {
        $member = User::factory()->create(['name' => 'Awa Kouamé']);

        $this->actingAs($member)->postJson('/communaute/projets', [
            'title' => 'Outil agricole ouvert',
            'pole' => 'Logiciel & Open Source',
            'status' => 'Prototype',
            'summary' => 'Une expérimentation ouverte pour les producteurs.',
            'body' => 'Description du projet membre.',
            'team_members' => "Awa Kouamé\nYao N’Guessan",
            'participant_consent' => '1',
        ])->assertCreated()
            ->assertJsonPath('status', 'pending_review')
            ->assertJsonStructure(['slug', 'message']);

        $project = PublicProject::firstOrFail();
        $this->assertSame($member->id, $project->user_id);
        $this->assertNull($project->published_at);
        $this->assertSame(['Awa Kouamé', 'Yao N’Guessan'], $project->team_members);
    }

    public function test_member_can_submit_a_completed_project_with_private_images_for_review(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $member = User::factory()->create();

        $this->actingAs($member)->post(route('community.projects.store'), [
            'title' => 'Application de suivi des cultures',
            'pole' => 'Logiciel & Open Source',
            'status' => 'Terminé',
            'summary' => 'Une application ouverte pour suivre les cultures.',
            'body' => 'Le projet est terminé et disponible en démonstration.',
            'repo_url' => 'https://example.ci/projet',
            'participant_consent' => '1',
            'images' => [$this->fakePng('prototype.png')],
        ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('status', 'pending_review');

        $project = PublicProject::with('images')->firstOrFail();
        $image = $project->images->sole();
        $this->assertSame('Terminé', $project->status);
        $this->assertSame('https://example.ci/projet', $project->repo_url);
        Storage::disk('local')->assertExists($image->path);
        Storage::disk('public')->assertMissing($image->path);
        $this->actingAs($member)
            ->get(route('admin.public-projects.images.show', [$project, $image]))
            ->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.public-projects.images.show', [$project, $image]))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->getJson("/api/public/projects/{$project->slug}")
            ->assertNotFound();
        $this->get("/api/public/projects/{$project->slug}/images/{$image->id}")
            ->assertNotFound();

        $project->update(['published_at' => now()->subMinute()]);

        $this->getJson("/api/public/projects/{$project->slug}")
            ->assertOk()
            ->assertJsonPath('project.status', 'Terminé')
            ->assertJsonPath('project.repo_url', 'https://example.ci/projet')
            ->assertJsonPath('project.images.0.id', $image->id)
            ->assertJsonPath('project.images.0.url', "/api/public/projects/{$project->slug}/images/{$image->id}");

        $this->get("/api/public/projects/{$project->slug}/images/{$image->id}")
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $project->update(['published_at' => null]);
        $this->actingAs($member)
            ->get(route('community.projects.images.show', [$project, $image]))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Cache-Control', 'no-store, private');

        $this->actingAs(User::factory()->create())
            ->get(route('community.projects.images.show', [$project, $image]))
            ->assertForbidden();
    }

    public function test_member_project_images_must_use_a_supported_image_format(): void
    {
        $member = User::factory()->create();
        $payload = [
            'title' => 'Projet avec fichier invalide',
            'pole' => 'Formation',
            'status' => 'Terminé',
            'summary' => 'Un projet de formation terminé.',
            'body' => 'Description du projet.',
            'participant_consent' => '1',
            'images' => [UploadedFile::fake()->create('document.txt', 10, 'text/plain')],
        ];

        $this->actingAs($member)->post(route('community.projects.store'), $payload, ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('images.0');
        $this->assertSame(0, PublicProject::count());
    }

    public function test_member_project_submission_is_limited_to_five_images(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->post(route('community.projects.store'), [
            'title' => 'Projet avec trop d’images',
            'pole' => 'Formation',
            'status' => 'Terminé',
            'summary' => 'Un projet de formation terminé.',
            'body' => 'Description du projet.',
            'participant_consent' => '1',
            'images' => array_map(
                fn (int $index) => $this->fakePng("image-{$index}.png"),
                range(1, 6)
            ),
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('images');

        $this->assertSame(0, PublicProject::count());
    }

    public function test_report_is_private_until_project_and_report_are_approved_and_replacement_revokes_approval(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $member = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($member)->post(route('community.projects.store'), [
            'title' => 'Documentation du projet',
            'pole' => 'Formation',
            'status' => 'Terminé',
            'summary' => 'Un projet documenté pour la communauté.',
            'body' => 'Description complète du projet.',
            'participant_consent' => '1',
            'report' => UploadedFile::fake()->create('rapport.pdf', 32, 'application/pdf'),
        ], ['Accept' => 'application/json'])->assertCreated();

        $project = PublicProject::with('report')->firstOrFail();
        $originalReport = $project->report;
        $this->assertNotNull($originalReport);
        $this->assertNull($originalReport->approved_at);
        Storage::disk('local')->assertExists($originalReport->path);
        Storage::disk('public')->assertMissing($originalReport->path);
        $this->get("/api/public/projects/{$project->slug}/report")->assertNotFound();

        $project->update(['published_at' => now()->subMinute()]);
        $this->get("/api/public/projects/{$project->slug}/report")->assertNotFound();
        $this->getJson("/api/public/projects/{$project->slug}")
            ->assertOk()
            ->assertJsonPath('project.has_report', false)
            ->assertJsonPath('project.report', null);

        $this->actingAs($admin)->put(route('admin.public-projects.update', $project), [
            'title' => $project->title,
            'slug' => $project->slug,
            'pole' => $project->pole,
            'status' => $project->status,
            'summary' => $project->summary,
            'body' => $project->body,
            'team_members' => '',
            'repo_url' => '',
            'publish' => '1',
            'approve_report' => '1',
        ])->assertRedirect(route('admin.public-projects.index'));

        $project->refresh()->load('report');
        $this->assertNotNull($project->report->approved_at);
        $this->get("/api/public/projects/{$project->slug}/report")
            ->assertOk()
            ->assertDownload('rapport.pdf');
        $this->getJson("/api/public/projects/{$project->slug}")
            ->assertOk()
            ->assertJsonPath('project.has_report', true)
            ->assertJsonPath('project.report.name', 'rapport.pdf');

        $this->actingAs($member)->post(route('community.projects.report.store', $project), [
            'report' => UploadedFile::fake()->create('rapport-mis-a-jour.pdf', 24, 'application/pdf'),
        ])->assertRedirect(route('community.projects.index'));

        $project->refresh()->load('report');
        $this->assertNull($project->report->approved_at);
        Storage::disk('local')->assertMissing($originalReport->path);
        Storage::disk('local')->assertExists($project->report->path);
        $this->get("/api/public/projects/{$project->slug}/report")->assertNotFound();
        $this->getJson("/api/public/projects/{$project->slug}")
            ->assertOk()
            ->assertJsonPath('project.has_report', false)
            ->assertJsonPath('project.report', null);

        $this->actingAs($admin)
            ->delete(route('admin.public-projects.destroy', $project))
            ->assertRedirect();
        Storage::disk('local')->assertMissing($project->report->path);
    }
}
