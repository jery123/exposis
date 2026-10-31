<?php
namespace Tests\Feature;

use App\Models\Project;
use App\Models\PublicProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectAccessTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $o = []): array
    {
        return $o + ['title' => 'Test', 'request_type' => 'feasibility', 'problem' => 'Un problème assez long.', 'description' => 'Une description assez longue.', 'consent' => '1'];
    }

    public function test_guest_is_redirected(): void { $this->get('/projects')->assertRedirect('/login'); }

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
            ->assertSee('En cours de validation');
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
}
