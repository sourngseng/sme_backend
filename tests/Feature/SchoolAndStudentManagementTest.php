<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\AuditLog;
use App\Models\Guardian;
use App\Models\Permission;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLocation;
use App\Models\SchoolMealPolicy;
use App\Models\SchoolShift;
use App\Models\Student;
use App\Models\StudentNutritionProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SchoolAndStudentManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $schoolAdminA;
    protected User $schoolAdminB;
    protected User $parentUserA;
    protected User $parentUserB;

    protected School $schoolA;
    protected School $schoolB;
    protected SchoolShift $shiftA;
    protected SchoolLocation $locationA;
    protected Student $studentA;
    protected Allergen $peanutAllergen;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], ['guard_name' => 'web']);
        $schoolAdminRole = Role::firstOrCreate(['name' => 'school_admin'], ['guard_name' => 'web']);
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['guard_name' => 'web']);

        // 2. Permissions
        $schoolPerms = [
            'schools.view', 'schools.create', 'schools.update', 'schools.delete',
            'schools.shifts.manage', 'schools.locations.manage', 'schools.policies.manage',
            'students.view', 'students.create', 'students.update', 'students.delete',
            'nutrition_profiles.view', 'nutrition_profiles.update',
        ];

        foreach ($schoolPerms as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName]);
            $schoolAdminRole->permissions()->syncWithoutDetaching([$perm->id]);
        }

        // 3. Schools
        $this->schoolA = School::create([
            'code' => 'SCH-TEST-A',
            'name' => 'Primary Academy A',
            'level' => 'primary',
            'status' => 'active',
            'phone' => '+85512000101',
            'address' => 'Street 101, Phnom Penh',
        ]);

        $this->schoolB = School::create([
            'code' => 'SCH-TEST-B',
            'name' => 'High School B',
            'level' => 'high_school',
            'status' => 'active',
            'phone' => '+85512000102',
            'address' => 'Street 102, Phnom Penh',
        ]);

        // Shifts & Locations for School A
        $this->shiftA = SchoolShift::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Morning Session',
            'start_time' => '07:30:00',
            'end_time' => '11:30:00',
            'status' => 'active',
        ]);

        $this->locationA = SchoolLocation::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Canteen Counter 1',
            'address' => 'Ground Floor',
            'status' => 'active',
        ]);

        // 4. Users
        $this->superAdmin = User::create([
            'full_name' => 'Super Admin Tester',
            'email' => 'superadmin.test@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->superAdmin->assignRole('super_admin');

        $this->schoolAdminA = User::create([
            'full_name' => 'Admin of School A',
            'email' => 'admin.schoola@example.com',
            'password' => 'Password123!',
            'status' => 'active',
            'school_id' => $this->schoolA->id,
        ]);
        $this->schoolAdminA->assignRole('school_admin');

        $this->schoolAdminB = User::create([
            'full_name' => 'Admin of School B',
            'email' => 'admin.schoolb@example.com',
            'password' => 'Password123!',
            'status' => 'active',
            'school_id' => $this->schoolB->id,
        ]);
        $this->schoolAdminB->assignRole('school_admin');

        $this->parentUserA = User::create([
            'full_name' => 'Parent User A',
            'email' => 'parent.a@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->parentUserA->assignRole('parent');
        Guardian::firstOrCreate(['user_id' => $this->parentUserA->id]);

        $this->parentUserB = User::create([
            'full_name' => 'Parent User B',
            'email' => 'parent.b@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->parentUserB->assignRole('parent');
        Guardian::firstOrCreate(['user_id' => $this->parentUserB->id]);

        // 5. Students
        $this->studentA = Student::create([
            'student_code' => 'STU-TEST-001',
            'school_id' => $this->schoolA->id,
            'school_shift_id' => $this->shiftA->id,
            'full_name' => 'Alice Seng',
            'date_of_birth' => '2015-08-10',
            'gender' => 'female',
            'grade_level' => 'Grade 5',
            'class_name' => '5-B',
            'status' => 'active',
        ]);

        $this->peanutAllergen = Allergen::firstOrCreate(
            ['code' => 'PEANUTS'],
            ['name' => 'Peanuts', 'description' => 'Peanut products']
        );
    }

    public function test_super_admin_can_crud_schools(): void
    {
        // 1. Create School
        $createRes = $this->actingAs($this->superAdmin, 'sanctum')->postJson('/api/v1/schools', [
            'code' => 'NEW-SCH-01',
            'name' => 'New Pioneer Academy',
            'level' => 'secondary',
            'status' => 'active',
            'phone' => '+85523999111',
            'email' => 'contact@pioneer.edu.kh',
            'address' => 'Toul Kork, Phnom Penh',
        ]);

        $createRes->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.code', 'NEW-SCH-01');

        $newSchoolId = $createRes->json('data.id');

        // 2. View School
        $showRes = $this->actingAs($this->superAdmin, 'sanctum')->getJson("/api/v1/schools/{$newSchoolId}");
        $showRes->assertStatus(200)
            ->assertJsonPath('data.name', 'New Pioneer Academy');

        // 3. Update School
        $updateRes = $this->actingAs($this->superAdmin, 'sanctum')->putJson("/api/v1/schools/{$newSchoolId}", [
            'name' => 'New Pioneer Academy Updated',
        ]);
        $updateRes->assertStatus(200)
            ->assertJsonPath('data.name', 'New Pioneer Academy Updated');

        // 4. Delete School
        $deleteRes = $this->actingAs($this->superAdmin, 'sanctum')->deleteJson("/api/v1/schools/{$newSchoolId}");
        $deleteRes->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('schools', ['id' => $newSchoolId]);
    }

    public function test_school_admin_crud_shifts_and_locations_for_their_school(): void
    {
        // 1. School Admin A creates a shift for School A
        $shiftRes = $this->actingAs($this->schoolAdminA, 'sanctum')
            ->postJson("/api/v1/schools/{$this->schoolA->id}/shifts", [
                'name' => 'Afternoon Shift',
                'start_time' => '12:30:00',
                'end_time' => '16:30:00',
                'status' => 'active',
            ]);

        $shiftRes->assertStatus(201)
            ->assertJsonPath('data.name', 'Afternoon Shift')
            ->assertJsonPath('data.school_id', $this->schoolA->id);

        $newShiftId = $shiftRes->json('data.id');

        // 2. School Admin A creates a location for School A
        $locationRes = $this->actingAs($this->schoolAdminA, 'sanctum')
            ->postJson("/api/v1/schools/{$this->schoolA->id}/locations", [
                'name' => 'Gate B Drop-off',
                'address' => 'North Gate',
                'instructions' => 'Drop at reception table',
                'status' => 'active',
            ]);

        $locationRes->assertStatus(201)
            ->assertJsonPath('data.name', 'Gate B Drop-off')
            ->assertJsonPath('data.school_id', $this->schoolA->id);

        // 3. School Admin A updates School A meal policy
        $policyRes = $this->actingAs($this->schoolAdminA, 'sanctum')
            ->putJson("/api/v1/schools/{$this->schoolA->id}/meal-policy", [
                'default_cutoff_minutes' => 90,
                'allowed_meal_types' => ['lunch', 'snack'],
                'allow_cod' => false,
            ]);

        $policyRes->assertStatus(200)
            ->assertJsonPath('data.default_cutoff_minutes', 90)
            ->assertJsonPath('data.allowed_meal_types', ['lunch', 'snack']);
    }

    public function test_school_admin_cannot_access_other_schools_resources(): void
    {
        // School Admin A attempts to create a shift in School B
        $forbiddenShift = $this->actingAs($this->schoolAdminA, 'sanctum')
            ->postJson("/api/v1/schools/{$this->schoolB->id}/shifts", [
                'name' => 'Intruder Shift',
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
            ]);

        $forbiddenShift->assertStatus(403);

        // School Admin A attempts to update School B meal policy
        $forbiddenPolicy = $this->actingAs($this->schoolAdminA, 'sanctum')
            ->putJson("/api/v1/schools/{$this->schoolB->id}/meal-policy", [
                'default_cutoff_minutes' => 30,
            ]);

        $forbiddenPolicy->assertStatus(403);
    }

    public function test_student_search_and_school_admin_object_level_filtering(): void
    {
        // Create student in School B
        $studentB = Student::create([
            'student_code' => 'STU-TEST-002',
            'school_id' => $this->schoolB->id,
            'full_name' => 'Bob Sovann',
            'grade_level' => 'Grade 10',
            'class_name' => '10-A',
            'status' => 'active',
        ]);

        // 1. Super Admin can search all students across schools
        $allRes = $this->actingAs($this->superAdmin, 'sanctum')->getJson('/api/v1/students');
        $allRes->assertStatus(200);
        $studentCodes = collect($allRes->json('data'))->pluck('student_code');
        $this->assertTrue($studentCodes->contains('STU-TEST-001'));
        $this->assertTrue($studentCodes->contains('STU-TEST-002'));

        // 2. School Admin A can ONLY see students in School A
        $adminARes = $this->actingAs($this->schoolAdminA, 'sanctum')->getJson('/api/v1/students');
        $adminARes->assertStatus(200);
        $adminACodes = collect($adminARes->json('data'))->pluck('student_code');
        $this->assertTrue($adminACodes->contains('STU-TEST-001'));
        $this->assertFalse($adminACodes->contains('STU-TEST-002'));

        // 3. Search with filters: full_name and grade_level
        $filterRes = $this->actingAs($this->superAdmin, 'sanctum')
            ->getJson('/api/v1/students?full_name=Alice&grade_level=Grade 5');
        $filterRes->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.student_code', 'STU-TEST-001');
    }

    public function test_parent_can_register_and_link_child_with_permissions(): void
    {
        $regRes = $this->actingAs($this->parentUserA, 'sanctum')->postJson('/api/v1/guardian/students', [
            'school_id' => $this->schoolA->id,
            'school_shift_id' => $this->shiftA->id,
            'full_name' => 'Dara Seng',
            'date_of_birth' => '2016-04-12',
            'gender' => 'male',
            'grade_level' => 'Grade 4',
            'class_name' => '4-A',
            'relationship' => 'Father',
            'is_primary' => true,
            'can_order' => true,
            'can_view_nutrition' => true,
            'dietary_preferences' => ['Halal friendly', 'No nuts'],
            'allergen_ids' => [$this->peanutAllergen->id],
            'nutrition_notes' => 'Severe peanut allergy',
        ]);

        $regRes->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.full_name', 'Dara Seng')
            ->assertJsonPath('data.guardian_permissions.is_primary', true)
            ->assertJsonPath('data.guardian_permissions.can_order', true)
            ->assertJsonPath('data.guardian_permissions.can_view_nutrition', true);

        $newStudentId = $regRes->json('data.id');

        // Verify linked student list for Parent A
        $parentList = $this->actingAs($this->parentUserA, 'sanctum')->getJson('/api/v1/guardian/students');
        $parentList->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $newStudentId);

        // Verify nutrition profile was created and reflects allergens
        $this->assertDatabaseHas('student_nutrition_profiles', [
            'student_id' => $newStudentId,
            'notes' => 'Severe peanut allergy',
        ]);
    }

    public function test_parent_can_update_child_dietary_preferences_and_allergies(): void
    {
        // Link studentA to parentUserA
        $guardianA = Guardian::where('user_id', $this->parentUserA->id)->first();
        $guardianA->students()->syncWithoutDetaching([
            $this->studentA->id => [
                'is_primary' => true,
                'can_order' => true,
                'can_view_nutrition' => true,
            ],
        ]);

        // Parent A updates nutrition profile
        $updateRes = $this->actingAs($this->parentUserA, 'sanctum')
            ->putJson("/api/v1/guardian/students/{$this->studentA->id}/nutrition-profile", [
                'dietary_preferences' => ['Vegetarian', 'Low sodium'],
                'allergen_ids' => [$this->peanutAllergen->id],
                'notes' => 'Strict vegetarian diet.',
            ]);

        $updateRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.dietary_preferences', ['Vegetarian', 'Low sodium'])
            ->assertJsonPath('data.notes', 'Strict vegetarian diet.');

        $this->assertDatabaseHas('student_nutrition_profiles', [
            'student_id' => $this->studentA->id,
            'notes' => 'Strict vegetarian diet.',
        ]);
    }

    public function test_unlinked_parent_cannot_view_or_modify_other_children(): void
    {
        // Student A is NOT linked to Parent B
        $forbiddenView = $this->actingAs($this->parentUserB, 'sanctum')
            ->getJson("/api/v1/guardian/students/{$this->studentA->id}");

        $forbiddenView->assertStatus(403);

        $forbiddenNutrition = $this->actingAs($this->parentUserB, 'sanctum')
            ->putJson("/api/v1/guardian/students/{$this->studentA->id}/nutrition-profile", [
                'notes' => 'Hacker note',
            ]);

        $forbiddenNutrition->assertStatus(403);
    }
}
