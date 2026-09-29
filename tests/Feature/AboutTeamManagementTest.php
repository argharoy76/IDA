<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\TeamMember;

class AboutTeamManagementTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clean up any test uploads
        $testDir = public_path('uploads/cms/team');
        if (File::isDirectory($testDir)) {
            $files = File::files($testDir);
            foreach ($files as $file) {
                if (str_contains($file->getFilename(), 'test_')) {
                    @unlink($file->getPathname());
                }
            }
        }
        parent::tearDown();
    }

    public function test_public_about_page_loads_with_team_members()
    {
        TeamMember::firstOrCreate([
            'name' => 'Argha Roy',
        ], [
            'designation' => 'Founder & Chief Executive Officer (CEO)',
            'bio' => 'Visionary founder committed to modernizing military preparatory education in Bangladesh.',
            'display_category' => 1,
            'display_order' => 10,
            'is_active' => true,
        ]);

        $response = $this->get(route('about'));
        $response->assertStatus(200);
        $response->assertSee('Meet the Team');
        $response->assertSee('Argha Roy');
        $response->assertSee('Chief Executive Officer');
    }

    public function test_admin_can_access_about_cms_editor()
    {
        $admin = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($admin)->get(route('admin.cms.about'));

        $response->assertStatus(200);
        $response->assertSee('About Page Editor');
        $response->assertSee('About Hero Section');
        $response->assertSee('Directing Staff');
        $response->assertSee('Add New Member');
    }

    public function test_admin_can_store_update_reorder_and_delete_team_member()
    {
        $admin = User::where('role', 'super_admin')->first();

        // 1. Store
        $storeResponse = $this->actingAs($admin)->post(route('admin.cms.team.store'), [
            'name' => 'General (Retd.) M. Rahman',
            'designation' => 'Chief Patron & Strategic Advisor',
            'bio' => 'Advising on long-term institutional vision and strategic military alliances.',
            'display_category' => 1,
            'display_order' => 5,
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('team_members', [
            'name' => 'General (Retd.) M. Rahman',
            'designation' => 'Chief Patron & Strategic Advisor',
            'display_category' => 1,
        ]);

        $member = TeamMember::where('name', 'General (Retd.) M. Rahman')->first();

        // 2. Update
        $updateResponse = $this->actingAs($admin)->put(route('admin.cms.team.update', $member->id), [
            'name' => 'General (Retd.) Mustafizur Rahman',
            'designation' => 'Senior Strategic Advisor & Honorary Patron',
            'bio' => 'Updated biography details for strategic leadership.',
            'display_category' => 1,
            'display_order' => 5,
            'is_active' => true,
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('team_members', [
            'id' => $member->id,
            'name' => 'General (Retd.) Mustafizur Rahman',
            'designation' => 'Senior Strategic Advisor & Honorary Patron',
        ]);

        // 3. Reorder
        $reorderResponse = $this->actingAs($admin)->post(route('admin.cms.team.reorder', $member->id), [
            'direction' => 'down',
        ]);
        $reorderResponse->assertRedirect();

        // 4. Delete
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.cms.team.delete', $member->id));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('team_members', [
            'id' => $member->id,
        ]);
    }

    public function test_admin_can_update_about_hero_settings()
    {
        $admin = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($admin)->post(route('admin.cms.settings'), [
            'about_hero_title' => 'Inspiring Bangladesh Officers',
            'about_hero_subtitle' => 'Uncompromising physical, mental, and tactical excellence.',
            'about_hero_overlay_text' => 'We prepare candidates with authentic military leadership standards.',
            'about_title' => 'Imperial Defence Academy at Khulna',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Inspiring Bangladesh Officers', cms('about_hero_title'));
        $this->assertEquals('Imperial Defence Academy at Khulna', cms('about_title'));

        // Verify it reflects on live frontend
        $frontendResponse = $this->get(route('about'));
        $frontendResponse->assertStatus(200);
        $frontendResponse->assertSee('Inspiring Bangladesh Officers');
        $frontendResponse->assertSee('Imperial Defence Academy at Khulna');
    }
}
