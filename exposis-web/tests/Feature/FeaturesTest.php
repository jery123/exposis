<?php
namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FeaturesTest extends TestCase
{
    use RefreshDatabase;

    private function project(User $u): Project
    {
        return $u->projects()->create(['title' => 'T', 'request_type' => 'feasibility', 'problem' => 'Un problème assez long.', 'description' => 'Une description assez longue.']);
    }

    public function test_internal_notes_are_hidden_from_the_organization(): void
    {
        $org = User::factory()->create(); $admin = User::factory()->create(['role' => 'admin']); $p = $this->project($org);
        $this->actingAs($admin)->post(route('projects.messages.store', $p), ['body' => 'note secrète', 'is_internal' => '1'])->assertRedirect();
        $this->actingAs($org)->post(route('projects.messages.store', $p), ['body' => 'bonjour équipe', 'is_internal' => '1'])->assertRedirect();
        $this->actingAs($org)->get(route('projects.show', $p))->assertSee('bonjour équipe')->assertDontSee('note secrète');
        $this->assertDatabaseHas('project_messages', ['body' => 'bonjour équipe', 'is_internal' => false]); // org cannot forge internal notes
    }

    public function test_files_are_private_to_owner_and_admin(): void
    {
        Storage::fake('local');
        [$a, $b] = User::factory()->count(2)->create(); $p = $this->project($a);
        $this->actingAs($a)->post(route('projects.files.store', $p), ['files' => [UploadedFile::fake()->create('plan.pdf', 100, 'application/pdf')]])->assertRedirect();
        $file = $p->files()->first();
        $this->actingAs($a)->get(route('files.download', $file))->assertOk();
        $this->actingAs($b)->get(route('files.download', $file))->assertForbidden();
    }

    public function test_dashboard_export_and_sitemap(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/projects/export')->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->get('/sitemap.xml')->assertOk();
    }
}
