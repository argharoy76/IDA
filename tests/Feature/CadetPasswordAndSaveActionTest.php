<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class CadetPasswordAndSaveActionTest extends TestCase
{
    public function test_save_update_action_redirects_to_edit_page(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $user = User::create([
            'name' => 'Save Update Test Cadet',
            'email' => 'save_update_' . uniqid() . '@ida.com',
            'account_id' => 'SU-' . strtoupper(uniqid()),
            'password' => Hash::make('password123'),
            'plain_password' => 'password123',
            'role' => 'academic_student',
            'phone' => '01711999888',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $user->account_id,
            'student_type' => 'offline',
            'gender' => 'male',
            'address' => 'Dhaka Bangladesh',
            'status' => 'active',
            'plain_password' => 'password123',
        ]);

        $response = $this->actingAs($admin)->put("/admin/student-accounts/{$student->id}", [
            'name' => 'Updated Name for Save Update',
            'custom_id' => $user->account_id,
            'phone' => $user->phone,
            'email' => $user->email,
            'gender' => 'male',
            'address' => 'Dhaka Bangladesh',
            'student_type' => 'offline',
            'submit_action' => 'save_update',
        ]);

        $response->assertRedirect(route('admin.student_accounts.edit', $student->id));
        $response->assertSessionHas('success');
    }

    public function test_ajax_save_update_completes_update_without_page_reload(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $user = User::create([
            'name' => 'Ajax Test Cadet',
            'email' => 'ajax_test_' . uniqid() . '@ida.com',
            'account_id' => 'AJAX-' . strtoupper(uniqid()),
            'password' => Hash::make('password123'),
            'plain_password' => 'password123',
            'role' => 'academic_student',
            'phone' => '01711888999',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $user->account_id,
            'student_type' => 'offline',
            'gender' => 'male',
            'address' => 'Barishal Bangladesh',
            'status' => 'active',
            'plain_password' => 'password123',
        ]);

        $response = $this->actingAs($admin)->putJson("/admin/student-accounts/{$student->id}", [
            'name' => 'Updated via Ajax No Reload',
            'custom_id' => $user->account_id,
            'phone' => '01711555444',
            'email' => $user->email,
            'gender' => 'male',
            'address' => 'Barishal Bangladesh',
            'student_type' => 'academic',
            'submit_action' => 'save_update',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'login_id',
            'name',
        ]);

        $user->refresh();
        $student->refresh();
        $this->assertEquals('Updated via Ajax No Reload', $user->name);
        $this->assertEquals('01711555444', $user->phone);
        $this->assertEquals('academic', $student->student_type);
    }

    public function test_save_changes_action_redirects_to_show_page(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $user = User::create([
            'name' => 'Save Changes Test Cadet',
            'email' => 'save_changes_' . uniqid() . '@ida.com',
            'account_id' => 'SC-' . strtoupper(uniqid()),
            'password' => Hash::make('password123'),
            'plain_password' => 'password123',
            'role' => 'academic_student',
            'phone' => '01711999777',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $user->account_id,
            'student_type' => 'offline',
            'gender' => 'male',
            'address' => 'Chattogram Bangladesh',
            'status' => 'active',
            'plain_password' => 'password123',
        ]);

        $response = $this->actingAs($admin)->put("/admin/student-accounts/{$student->id}", [
            'name' => 'Updated Name for Save Changes',
            'custom_id' => $user->account_id,
            'phone' => $user->phone,
            'email' => $user->email,
            'gender' => 'male',
            'address' => 'Chattogram Bangladesh',
            'student_type' => 'offline',
            'submit_action' => 'save_changes',
        ]);

        $response->assertRedirect(route('admin.student_accounts.show', $student->id));
        $response->assertSessionHas('success');
    }

    public function test_reset_password_optional_text_removed_and_permissions_enforced(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $secretCadetPassword = 'MySecretCadetPass99!';
        $user = User::create([
            'name' => 'Secret Pass Cadet',
            'email' => 'secret_cadet_' . uniqid() . '@ida.com',
            'account_id' => 'SEC-' . strtoupper(uniqid()),
            'password' => Hash::make($secretCadetPassword),
            'plain_password' => $secretCadetPassword,
            'role' => 'academic_student',
            'phone' => '01711999666',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_code' => $user->account_id,
            'student_type' => 'offline',
            'gender' => 'male',
            'address' => 'Sylhet Bangladesh',
            'status' => 'active',
            'plain_password' => $secretCadetPassword,
        ]);

        // 1. Super Admin sees Reset Password without (Optional), and sees cadet current password
        $resSuper = $this->actingAs($superAdmin)->get("/admin/student-accounts/{$student->id}/edit");
        $resSuper->assertStatus(200);
        $resSuper->assertDontSee('Reset Password (Optional)');
        $resSuper->assertSee('Reset Password');
        $resSuper->assertSee($secretCadetPassword);
        $resSuper->assertSee('cadet_current_password_input');

        // 2. Pro Admin WITH cadet password permission can see password
        $proAdminWithPerm = User::create([
            'name' => 'Pro Admin With Access',
            'email' => 'pro_with_' . uniqid() . '@ida.com',
            'account_id' => 'PRO-W-' . strtoupper(uniqid()),
            'phone' => '01700111222',
            'password' => Hash::make('proadmin123'),
            'role' => 'pro_admin',
            'permissions' => ['can_access_cadet_passwords'],
            'status' => 'active',
        ]);

        $this->assertTrue($proAdminWithPerm->canAccessCadetPasswords());

        $resProWith = $this->actingAs($proAdminWithPerm)->get("/admin/student-accounts/{$student->id}/edit");
        $resProWith->assertStatus(200);
        $resProWith->assertSee($secretCadetPassword);

        // 3. Pro Admin WITHOUT cadet password permission cannot see plain password
        $proAdminWithoutPerm = User::create([
            'name' => 'Pro Admin Restricted',
            'email' => 'pro_without_' . uniqid() . '@ida.com',
            'account_id' => 'PRO-WO-' . strtoupper(uniqid()),
            'phone' => '01700111333',
            'password' => Hash::make('proadmin123'),
            'role' => 'pro_admin',
            'permissions' => [],
            'status' => 'active',
        ]);

        $this->assertFalse($proAdminWithoutPerm->canAccessCadetPasswords());

        $resProWithout = $this->actingAs($proAdminWithoutPerm)->get("/admin/student-accounts/{$student->id}/edit");
        $resProWithout->assertStatus(200);
        $resProWithout->assertDontSee($secretCadetPassword);
        $resProWithout->assertSee('•••••••• (Restricted)');
    }

    public function test_admin_control_can_toggle_pro_admin_cadet_password_access(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // Developer / Super Admin creates Pro Admin with cadet password access enabled
        $targetProAdmin = User::create([
            'name' => 'Pro Admin Target',
            'email' => 'pro_target_' . uniqid() . '@ida.com',
            'account_id' => 'PRO-T-' . strtoupper(uniqid()),
            'phone' => '01700111444',
            'password' => Hash::make('targetpass123'),
            'role' => 'pro_admin',
            'permissions' => [],
            'status' => 'active',
        ]);

        $this->assertFalse($targetProAdmin->canAccessCadetPasswords());

        // Grant permission via can_access_cadet_passwords_opt = 1
        $updateRes = $this->actingAs($superAdmin)->put("/admin/admin-control/{$targetProAdmin->id}", [
            'name' => $targetProAdmin->name,
            'account_id' => $targetProAdmin->account_id,
            'email' => $targetProAdmin->email,
            'phone' => $targetProAdmin->phone,
            'role' => 'pro_admin',
            'status' => 'active',
            'can_access_cadet_passwords_opt' => '1',
            'submit_action' => 'save_update',
        ]);

        $updateRes->assertRedirect(route('admin.admin_control.edit', $targetProAdmin->id));
        $targetProAdmin->refresh();
        $this->assertTrue($targetProAdmin->canAccessCadetPasswords());
        $this->assertContains('can_access_cadet_passwords', $targetProAdmin->permissions);

        // Revoke permission via can_access_cadet_passwords_opt = 0
        $revokeRes = $this->actingAs($superAdmin)->put("/admin/admin-control/{$targetProAdmin->id}", [
            'name' => $targetProAdmin->name,
            'account_id' => $targetProAdmin->account_id,
            'email' => $targetProAdmin->email,
            'phone' => $targetProAdmin->phone,
            'role' => 'pro_admin',
            'status' => 'active',
            'can_access_cadet_passwords_opt' => '0',
            'submit_action' => 'save_changes',
        ]);

        $revokeRes->assertRedirect(route('admin.admin_control.index'));
        $targetProAdmin->refresh();
        $this->assertFalse($targetProAdmin->canAccessCadetPasswords());
        $this->assertNotContains('can_access_cadet_passwords', $targetProAdmin->permissions);
    }
}
