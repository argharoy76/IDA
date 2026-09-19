<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;

class DeploymentAndPathingTest extends TestCase
{
    public function test_cadet_portal_link_for_unauthenticated_visitor_points_to_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // The Cadet Portal link should point to the login route when unauthenticated
        $loginUrl = route('login');
        $response->assertSee($loginUrl);
    }

    public function test_cadet_shortcut_route_redirects_unauthenticated_user_to_login(): void
    {
        $response = $this->get('/cadet');
        $response->assertRedirect(route('login'));
    }

    public function test_portal_shortcut_route_redirects_unauthenticated_user_to_login(): void
    {
        $response = $this->get('/portal');
        $response->assertRedirect(route('login'));
    }

    public function test_academic_cadet_lands_on_cadet_dashboard(): void
    {
        $uid = uniqid();
        $cadetUser = User::create([
            'name' => 'Cadet Tester',
            'role' => 'academic_student',
            'email' => "cadet.{$uid}@ida.com",
            'phone' => "017" . rand(10000000, 99999999),
            'account_id' => "CAD-{$uid}",
            'password' => bcrypt('password'),
        ]);

        $student = Student::create([
            'user_id' => $cadetUser->id,
            'student_id_code' => "IDA-CADET-{$uid}",
            'target_wing' => 'Army',
            'status' => 'active',
        ]);

        $this->actingAs($cadetUser);

        // Access via shortcut /cadet redirects to Cadet Dashboard
        $response = $this->get('/cadet');
        $response->assertRedirect(route('cadet.dashboard'));

        // Access dashboard directly
        $dashResponse = $this->get(route('cadet.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Cadet Dashboard');
    }

    public function test_external_student_nav_link_and_shortcut_route_smoothly_to_candidate_portal(): void
    {
        $uid = uniqid();
        $externalUser = User::create([
            'name' => 'Candidate Tester',
            'role' => 'external_student',
            'email' => "candidate.{$uid}@ida.com",
            'phone' => "018" . rand(10000000, 99999999),
            'account_id' => "EXT-{$uid}",
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($externalUser);

        // Navbar Cadet Portal link on public homepage dynamically points to external candidate portal
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee(route('external.dashboard'));

        // Shortcut /cadet smoothly redirects to candidate dashboard
        $response = $this->get('/cadet');
        $response->assertRedirect(route('external.dashboard'));

        // Direct attempt to enter protected cadet-only dashboard enforces RBAC 403
        $directResponse = $this->get(route('cadet.dashboard'));
        $directResponse->assertStatus(403);
    }

    public function test_admin_and_developer_admin_can_access_cadet_portal_dashboard(): void
    {
        $admin = User::where('account_id', 'ArghaRoy')->first();
        if (!$admin) {
            $admin = User::where('role', 'super_admin')->first();
        }

        $this->actingAs($admin);

        // Admin accessing cadet dashboard should succeed
        $response = $this->get(route('cadet.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Cadet Dashboard');
    }

    public function test_trusted_proxy_and_forwarded_proto_headers_are_supported(): void
    {
        // Simulate reverse proxy forwarding from Hostinger (HTTPS terminating at proxy)
        $response = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-For' => '203.0.113.195',
            'X-Forwarded-Host' => 'ida.example.com',
        ])->get('/');

        $response->assertStatus(200);
    }
}
