<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Routine;
use App\Models\Invoice;
use App\Models\Exam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class DeveloperAdminControlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure ArghaRoy developer admin account is present
        $devAdmin = User::where('account_id', 'ArghaRoy')->first();
        if (!$devAdmin) {
            $devAdmin = User::create([
                'name' => 'ArghaRoy',
                'email' => 'argharoy@ida.com',
                'account_id' => 'ArghaRoy',
                'phone' => '+880 1711-001122',
                'password' => Hash::make('ArghaArghaGTA6'),
                'role' => 'super_admin',
                'status' => 'active',
                'permissions' => ['can_manage_cms', 'can_manage_exams', 'can_manage_students', 'can_manage_finance', 'can_view_audit'],
            ]);
        } else {
            $devAdmin->password = Hash::make('ArghaArghaGTA6');
            $devAdmin->phone = '+880 1711-001122';
            $devAdmin->role = 'super_admin';
            $devAdmin->save();
        }
    }

    /**
     * Test 1: Developer Admin can authenticate with 3 matching factors: ID, phone, and password
     */
    public function test_developer_admin_can_login_with_all_three_factors_matching(): void
    {
        $response = $this->post('/admin/login', [
            'login_id' => 'ArghaRoy',
            'phone' => '01711001122',
            'password' => 'ArghaArghaGTA6',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isDeveloperAdmin());
    }

    /**
     * Test 2: Admin login fails if phone does not match registered phone
     */
    public function test_admin_login_rejects_when_phone_does_not_match(): void
    {
        $response = $this->post('/admin/login', [
            'login_id' => 'ArghaRoy',
            'phone' => '01999999999', // Wrong phone
            'password' => 'ArghaArghaGTA6',
        ]);

        $response->assertSessionHasErrors('login_id');
        $this->assertGuest();
    }

    /**
     * Test 3: Admin login fails if password does not match
     */
    public function test_admin_login_rejects_when_password_does_not_match(): void
    {
        $response = $this->post('/admin/login', [
            'login_id' => 'ArghaRoy',
            'phone' => '01711001122',
            'password' => 'WrongPassword123',
        ]);

        $response->assertSessionHasErrors('login_id');
        $this->assertGuest();
    }

    /**
     * Test 4: Admin login fails if account ID does not match
     */
    public function test_admin_login_rejects_when_id_does_not_match(): void
    {
        $response = $this->post('/admin/login', [
            'login_id' => 'NonExistentAdmin',
            'phone' => '01711001122',
            'password' => 'ArghaArghaGTA6',
        ]);

        $response->assertSessionHasErrors('login_id');
        $this->assertGuest();
    }

    /**
     * Test 5: Only Developer Admin (ArghaRoy) sees "Admin Control" in menu
     */
    public function test_only_developer_admin_sees_admin_control_in_menu(): void
    {
        $devAdmin = User::where('account_id', 'ArghaRoy')->first();

        $response = $this->actingAs($devAdmin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Admin Control');
        $response->assertSee(route('admin.admin_control.index'));

        // Now test standard admin
        $standardAdmin = User::where('role', 'admin')->first();
        if (!$standardAdmin) {
            $standardAdmin = User::create([
                'name' => 'Standard Officer',
                'email' => 'officer@ida.com',
                'account_id' => 'ADM-STD-01',
                'phone' => '01811223344',
                'password' => Hash::make('SecretPass123'),
                'role' => 'admin',
                'status' => 'active',
            ]);
        }

        $stdResponse = $this->actingAs($standardAdmin)->get('/admin/dashboard');
        $stdResponse->assertStatus(200);
        $stdResponse->assertDontSee(route('admin.admin_control.index'));
        $stdResponse->assertDontSee('DEV ADMIN');
    }

    /**
     * Test 6: Non-developer admins are blocked with 403 on Admin Control routes
     */
    public function test_non_developer_admin_blocked_with_403_from_admin_control(): void
    {
        $standardAdmin = User::where('role', 'admin')->first();
        if (!$standardAdmin) {
            $standardAdmin = User::create([
                'name' => 'Standard Officer',
                'email' => 'officer2@ida.com',
                'account_id' => 'ADM-STD-02',
                'phone' => '01811223355',
                'password' => Hash::make('SecretPass123'),
                'role' => 'admin',
                'status' => 'active',
            ]);
        }

        $response = $this->actingAs($standardAdmin)->get('/admin/admin-control');
        $response->assertStatus(403);
    }

    /**
     * Test 7: Developer Admin can access Admin Control and provision a new admin with permissions
     */
    public function test_developer_admin_can_provision_new_admin_with_permissions(): void
    {
        $devAdmin = User::where('account_id', 'ArghaRoy')->first();

        $indexResponse = $this->actingAs($devAdmin)->get('/admin/admin-control');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Admin Control Command Center');
        $indexResponse->assertSee('Master Developer Console');

        $newAdminId = 'ADM-NEW-' . rand(100, 999);
        $postResponse = $this->actingAs($devAdmin)->post('/admin/admin-control', [
            'name' => 'Lieutenant Commander Nasir',
            'account_id' => $newAdminId,
            'email' => 'nasir_' . uniqid() . '@ida.com',
            'phone' => '01799887766',
            'password' => 'NasirPass123',
            'role' => 'admin',
            'permissions' => ['can_manage_exams', 'can_manage_cms'],
        ]);

        $postResponse->assertRedirect(route('admin.admin_control.index'));
        $postResponse->assertSessionHas('success');

        $created = User::where('account_id', $newAdminId)->first();
        $this->assertNotNull($created);
        $this->assertEquals('Lieutenant Commander Nasir', $created->name);
        $this->assertEquals('01799887766', $created->phone);
        $this->assertTrue($created->hasPermission('can_manage_exams'));
        $this->assertTrue($created->hasPermission('can_manage_cms'));
        $this->assertFalse($created->hasPermission('can_manage_finance'));
    }

    /**
     * Test 8: Developer Admin can update permissions and role of an existing admin
     */
    public function test_developer_admin_can_update_admin_permissions(): void
    {
        $devAdmin = User::where('account_id', 'ArghaRoy')->first();

        $targetAdmin = User::create([
            'name' => 'Major Farhan',
            'email' => 'farhan_' . uniqid() . '@ida.com',
            'account_id' => 'ADM-FARHAN-' . rand(10, 99),
            'phone' => '01611223344',
            'password' => Hash::make('Farhan123'),
            'role' => 'admin',
            'permissions' => ['can_manage_cms'],
            'status' => 'active',
        ]);

        $updateResponse = $this->actingAs($devAdmin)->post("/admin/admin-control/{$targetAdmin->id}/permissions", [
            'name' => 'Major Farhan Ahmed',
            'email' => $targetAdmin->email,
            'account_id' => $targetAdmin->account_id,
            'phone' => '01611998877',
            'role' => 'finance_manager',
            'permissions' => ['can_manage_cms', 'can_manage_finance'],
        ]);

        $updateResponse->assertRedirect(route('admin.admin_control.index'));
        $updateResponse->assertSessionHas('success');

        $fresh = $targetAdmin->fresh();
        $this->assertEquals('Major Farhan Ahmed', $fresh->name);
        $this->assertEquals('01611998877', $fresh->phone);
        $this->assertEquals('finance_manager', $fresh->role);
        $this->assertTrue($fresh->hasPermission('can_manage_finance'));
    }

    /**
     * Test 9: Developer Admin can delete an admin
     */
    public function test_developer_admin_can_delete_an_admin(): void
    {
        $devAdmin = User::where('account_id', 'ArghaRoy')->first();

        $tempAdmin = User::create([
            'name' => 'Temporary Officer',
            'email' => 'temp_' . uniqid() . '@ida.com',
            'account_id' => 'ADM-TEMP-' . rand(10, 99),
            'phone' => '01511223344',
            'password' => Hash::make('TempPass123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $deleteResponse = $this->actingAs($devAdmin)->delete("/admin/admin-control/{$tempAdmin->id}");
        $deleteResponse->assertRedirect(route('admin.admin_control.index'));
        $deleteResponse->assertSessionHas('success');

        $this->assertNull(User::find($tempAdmin->id));
    }

    /**
     * Test 10: Protection: Developer Admin ArghaRoy CANNOT be deleted
     */
    public function test_developer_admin_cannot_be_deleted(): void
    {
        $devAdmin = User::where('account_id', 'ArghaRoy')->first();

        $deleteResponse = $this->actingAs($devAdmin)->delete("/admin/admin-control/{$devAdmin->id}");
        $deleteResponse->assertRedirect(route('admin.admin_control.index'));
        $deleteResponse->assertSessionHas('error');

        $this->assertNotNull(User::find($devAdmin->id));
    }

    /**
     * Test 11: Admin can log in at cadet login page (/login) and access cadet dashboard
     */
    public function test_admin_can_login_at_cadet_portal_login(): void
    {
        $response = $this->post('/login', [
            'login_id' => 'ArghaRoy',
            'password' => 'ArghaArghaGTA6',
        ]);

        $response->assertRedirect(route('cadet.dashboard'));
        $this->assertAuthenticated();
    }

    /**
     * Test 12: Admin on cadet portal sees Routine, Payment, Previous Questions, and Other Courses
     */
    public function test_admin_on_cadet_portal_sees_hidden_modules(): void
    {
        $devAdmin = User::where('account_id', 'ArghaRoy')->first();

        // Cadet Dashboard
        $dashResponse = $this->actingAs($devAdmin)->get('/cadet/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Admin Clearance Mode');
        $dashResponse->assertSee('Routine');
        $dashResponse->assertSee('Payment');
        $dashResponse->assertSee('Other Courses');
        $dashResponse->assertSee('Admin View');

        // Cadet Routine
        $routineResponse = $this->actingAs($devAdmin)->get('/cadet/routine');
        $routineResponse->assertStatus(200);
        $routineResponse->assertSee('Admin Inspection Clearance Active');
        $routineResponse->assertDontSee('Routine Section Currently Hidden');

        // Cadet Fees
        $feesResponse = $this->actingAs($devAdmin)->get('/cadet/fees');
        $feesResponse->assertStatus(200);
        $feesResponse->assertSee('Admin Inspection Clearance Active');
        $feesResponse->assertDontSee('Payment Section Currently Hidden');

        // Cadet Exams
        $examsResponse = $this->actingAs($devAdmin)->get('/cadet/exams');
        $examsResponse->assertStatus(200);
    }
}
