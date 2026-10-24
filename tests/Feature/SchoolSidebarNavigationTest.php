<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolGrade;
use App\Models\SchoolShift;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolSidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    private User $schoolAdmin;
    private School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => 'school_admin'],
            ['display_name' => 'School Administrator', 'description' => 'Campus administration clearance']
        );

        $this->school = School::firstOrCreate(
            ['code' => 'WAT-001'],
            [
                'name' => 'Wat Bo Primary School',
                'level' => 'primary',
                'status' => 'active',
                'phone' => '+855 63 963 888',
                'email' => 'admin@watbo-school.edu.kh',
                'address' => 'Wat Bo Village, Siem Reap',
            ]
        );

        $this->schoolAdmin = User::factory()->create([
            'email' => 'schooladmin.tester@schoolmeal.test',
            'status' => 'active',
            'school_id' => $this->school->id,
        ]);
        $this->schoolAdmin->roles()->attach($role->id);
    }

    public function test_school_admin_left_sidebar_renders_all_prefixed_school_routes(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->get('/school/dashboard');

        $response->assertStatus(200);

        // Verify Brand Logo & Campus Context Card (from Image 1)
        $response->assertSee('SchoolMeal');
        $response->assertSee('E-Commerce & Nutrition Platform');
        $response->assertSee($this->school->name);
        $response->assertSee('School Admin');

        // Verify Left Sidebar Links all use the prefix /school/... (Image 1)
        $response->assertSee('/school/dashboard');
        $response->assertSee('/school/profile');
        $response->assertSee('/school/shifts');
        $response->assertSee('/school/locations');
        $response->assertSee('/school/students');
        $response->assertSee('/school/classes-grades');
        $response->assertSee('/school/roster');
        $response->assertSee('/school/orders');
        $response->assertSee('/school/delivery');
        $response->assertSee('/school/nutrition');
        $response->assertSee('/school/participation');
        $response->assertSee('/school/consumption');
        $response->assertSee('/school/reports');
        $response->assertSee('/school/announcements');
        $response->assertSee('/school/support');

        // Verify bottom switch school
        $response->assertSee('Switch School');
    }

    public function test_school_admin_can_access_all_left_sidebar_sections_with_200_ok(): void
    {
        $sections = [
            'students',
            'profile',
            'shifts',
            'locations',
            'classes-grades',
            'roster',
            'orders',
            'delivery',
            'nutrition',
            'participation',
            'consumption',
            'reports',
            'announcements',
            'support',
        ];

        foreach ($sections as $section) {
            $url = "/school/{$section}";
            $response = $this->actingAs($this->schoolAdmin)->get($url);

            $this->assertSame(
                200,
                $response->status(),
                "Expected 200 OK for school admin route {$url}, received {$response->status()}"
            );
        }
    }

    public function test_school_students_page_renders_exact_metrics_and_table_from_image_2(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->get('/school/students');

        $response->assertStatus(200);

        // Header
        $response->assertSee('Students');
        $response->assertSee('Manage students enrolled in your school.');
        $response->assertSee('Export');
        $response->assertSee('Add Student');

        // 4 Stat Cards
        $response->assertSee('Total Students');
        $response->assertSee('1,050');
        $response->assertSee('Active Students');
        $response->assertSee('980');
        $response->assertSee('93%');
        $response->assertSee('Meal Eligible');
        $response->assertSee('820');
        $response->assertSee('Allergy Alerts');
        $response->assertSee('42');

        // Filter Bar
        $response->assertSee('Search Student');
        $response->assertSee('Grade');
        $response->assertSee('Class');
        $response->assertSee('Meal Status');
        $response->assertSee('Allergy');
        $response->assertSee('Account Status');
        $response->assertSee('Reset Filters');

        // Reference Students from Image 2
        $response->assertSee('Sokha Piseth');
        $response->assertSee('STU0001');
        $response->assertSee('Chanthy Sreyneang');
        $response->assertSee('STU0002');
        $response->assertSee('Dara Vuthy');
        $response->assertSee('STU0003');
    }

    public function test_school_admin_can_enroll_new_student_via_post_form(): void
    {
        $payload = [
            'full_name' => 'Virak Sambo',
            'khmer_name' => 'វីរៈ សម្បត្តិ',
            'grade_level' => 'Grade 4',
            'class_name' => '4A',
            'gender' => 'male',
            'parent_name' => 'Sambo Chan',
            'parent_phone' => '+855 12 888 999',
            'meal_plan' => 'Regular Meal',
            'allergens' => ['Peanut'],
        ];

        $response = $this->actingAs($this->schoolAdmin)->post('/school/students', $payload);

        $response->assertRedirect('/school/students');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'full_name' => 'Virak Sambo',
            'grade_level' => 'Grade 4',
            'class_name' => '4A',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_school_sections_comply_strictly_with_toast_standards_and_no_inline_alerts(): void
    {
        $response = $this->actingAs($this->schoolAdmin)->get('/school/students');

        $content = $response->getContent();

        // Must include toast container
        $this->assertStringContainsString('toast-container', $content);

        // Never render inline alert boxes
        $this->assertStringNotContainsString('class="alert alert-danger"', $content);
        $this->assertStringNotContainsString('class="alert alert-warning"', $content);
        $this->assertStringNotContainsString('class="alert alert-success"', $content);
    }

    /**
     * Test Left Sidebar translates dynamically when switching languages (EN & KM).
     */
    public function test_school_admin_left_sidebar_translates_base_on_language_switcher(): void
    {
        // 1. Initial English view
        $enResponse = $this->actingAs($this->schoolAdmin)
            ->withSession(['locale' => 'en'])
            ->get('/school/students');

        $enResponse->assertStatus(200);
        $enResponse->assertSee('School Admin');
        $enResponse->assertSee('Dashboard');
        $enResponse->assertSee('SCHOOL MANAGEMENT');
        $enResponse->assertSee('School Profile');
        $enResponse->assertSee('School Shifts');
        $enResponse->assertSee('Locations');
        $enResponse->assertSee('Students');
        $enResponse->assertSee('Classes & Grades');
        $enResponse->assertSee('MEAL OPERATIONS');
        $enResponse->assertSee('Meal Roster');
        $enResponse->assertSee('Orders');
        $enResponse->assertSee('Delivery');
        $enResponse->assertSee('Nutrition & Allergies');
        $enResponse->assertSee('REPORTS');
        $enResponse->assertSee('Participation');
        $enResponse->assertSee('Consumption');
        $enResponse->assertSee('COMMUNICATION');
        $enResponse->assertSee('Announcements');
        $enResponse->assertSee('Support Tickets');
        $enResponse->assertSee('Switch School');

        // 2. Perform switch to Khmer via the locale.switch route
        $switchKm = $this->actingAs($this->schoolAdmin)
            ->from('/school/students')
            ->post(route('locale.switch'), ['locale' => 'km']);

        $switchKm->assertRedirect('/school/students');
        $switchKm->assertSessionHas('locale', 'km');

        // 3. Khmer view verification: all sidebar navigation elements translated to Khmer
        $kmResponse = $this->actingAs($this->schoolAdmin)
            ->withSession(['locale' => 'km'])
            ->get('/school/students');

        $kmResponse->assertStatus(200);
        $kmResponse->assertSee('អ្នកគ្រប់គ្រងសាលា'); // School Admin
        $kmResponse->assertSee('ផ្ទាំងគ្រប់គ្រង'); // Dashboard
        $kmResponse->assertSee('ការគ្រប់គ្រងសាលារៀន'); // SCHOOL MANAGEMENT
        $kmResponse->assertSee('ព័ត៌មានសាលារៀន'); // School Profile
        $kmResponse->assertSee('វេនសិក្សាសាលា'); // School Shifts
        $kmResponse->assertSee('ទីតាំង & បរិវេណ'); // Locations
        $kmResponse->assertSee('សិស្សានុសិស្ស'); // Students
        $kmResponse->assertSee('ថ្នាក់រៀន & កម្រិតថ្នាក់'); // Classes & Grades
        $kmResponse->assertSee('ប្រតិបត្តិការអាហារ'); // MEAL OPERATIONS
        $kmResponse->assertSee('បញ្ជីឈ្មោះអាហារ'); // Meal Roster
        $kmResponse->assertSee('ការបញ្ជាទិញ'); // Orders
        $kmResponse->assertSee('ការដឹកជញ្ជូន'); // Delivery
        $kmResponse->assertSee('អាហាររូបត្ថម្ភ & អាឡែកហ្ស៊ី'); // Nutrition & Allergies
        $kmResponse->assertSee('របាយការណ៍'); // REPORTS
        $kmResponse->assertSee('ការចូលរួម'); // Participation
        $kmResponse->assertSee('ការប្រើប្រាស់/បរិភោគ'); // Consumption
        $kmResponse->assertSee('ទំនាក់ទំនង'); // COMMUNICATION
        $kmResponse->assertSee('សេចក្តីជូនដំណឹង'); // Announcements
        $kmResponse->assertSee('សំបុត្រជំនួយ'); // Support Tickets
        $kmResponse->assertSee('ប្តូរសាលារៀន'); // Switch School

        // 4. Switch back to English
        $switchEn = $this->actingAs($this->schoolAdmin)
            ->from('/school/students')
            ->post(route('locale.switch'), ['locale' => 'en']);

        $switchEn->assertRedirect('/school/students');
        $switchEn->assertSessionHas('locale', 'en');

        $backEnResponse = $this->actingAs($this->schoolAdmin)
            ->withSession(['locale' => 'en'])
            ->get('/school/students');

        $backEnResponse->assertStatus(200);
        $backEnResponse->assertSee('Students');
        $backEnResponse->assertSee('School Admin');
    }

    public function test_school_students_ajax_server_side_pagination_and_search(): void
    {
        // 1. AJAX initial request returns JSON with partials
        $response = $this->actingAs($this->schoolAdmin)
            ->getJson('/school/students', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'section',
            'html',
            'pagination_html',
            'total',
            'from',
            'to',
            'stats',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertSame('students', $response->json('section'));
        $this->assertGreaterThanOrEqual(1, $response->json('total'));

        // 2. AJAX Search for specific student
        $searchResponse = $this->actingAs($this->schoolAdmin)
            ->getJson('/school/students?search=Piseth', ['X-Requested-With' => 'XMLHttpRequest']);

        $searchResponse->assertStatus(200);
        $searchData = $searchResponse->json();
        $this->assertTrue($searchData['success']);
        $this->assertStringContainsString('Sokha Piseth', $searchData['html']);
        $this->assertStringContainsString('STU0001', $searchData['html']);
    }

    public function test_school_students_ajax_filters_by_grade_and_class(): void
    {
        $response = $this->actingAs($this->schoolAdmin)
            ->getJson('/school/students?grade=Grade+4&class=4A', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertStringContainsString('4A', $data['html']);
    }

    public function test_school_admin_can_update_student_via_ajax_put(): void
    {
        // Ensure student exists via seeder
        $student = Student::where('school_id', $this->school->id)->first();
        if (!$student) {
            app(\Database\Seeders\StudentSeeder::class)->run();
            $student = Student::where('school_id', $this->school->id)->first();
        }

        $payload = [
            'full_name' => 'Sokha Piseth Updated',
            'khmer_name' => 'សុខា ពិសិដ្ឋ កែប្រែ',
            'gender' => 'male',
            'grade_level' => 'Grade 5',
            'class_name' => '5A',
            'status' => 'active',
            'parent_name' => 'Parent Updated',
            'parent_phone' => '+855 12 999 888',
        ];

        $response = $this->actingAs($this->schoolAdmin)
            ->putJson("/school/students/{$student->id}", $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'full_name' => 'Sokha Piseth Updated',
            'grade_level' => 'Grade 5',
            'class_name' => '5A',
        ]);
    }

    public function test_school_admin_can_delete_student_via_ajax_delete(): void
    {
        $student = Student::where('school_id', $this->school->id)->first();
        if (!$student) {
            app(\Database\Seeders\StudentSeeder::class)->run();
            $student = Student::where('school_id', $this->school->id)->first();
        }

        $response = $this->actingAs($this->schoolAdmin)
            ->deleteJson("/school/students/{$student->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertSoftDeleted('students', [
            'id' => $student->id,
        ]);
    }

    public function test_school_classes_grades_page_renders_meta_list_and_tabs_concept(): void
    {
        // Seed classes & grades
        app(\Database\Seeders\SchoolClassAndGradeSeeder::class)->run();

        $response = $this->actingAs($this->schoolAdmin)
            ->get('/school/classes-grades');

        $response->assertStatus(200);

        // Verify Tab triggers exist
        $response->assertSee('data-tab="classes"', false);
        $response->assertSee('data-tab="grades"', false);
        $response->assertSee('data-tab="rooms"', false);

        // Verify Meta Content List concept elements (Sticky columns & Context Menu)
        $response->assertSee('sticky-col-select', false);
        $response->assertSee('sticky-col-code', false);
        $response->assertSee('sticky-col-student', false);
        $response->assertSee('btn-context-menu', false);
        $response->assertSee('customizerMenuBtn', false);

        // Verify 4 KPI stat cards styled like Students UI
        $response->assertSee('Total Classes');
        $response->assertSee('Active Grades');
        $response->assertSee('Homeroom Staff');
        $response->assertSee('Pupil Capacity');
        $response->assertSee('↑ 2% vs last month');
        $response->assertSee('↑ 5% vs last month');
        $response->assertSee('↑ 8% vs last month');
        $response->assertSee('↑ 1% vs last month');
        $response->assertSee('gaugeCircleProgress', false);
        $response->assertSee('kpiActiveGradesPct', false);

        // Verify Multilingual Modals exist
        $response->assertSee('id="classModal"', false);
        $response->assertSee('id="gradeModal"', false);
        $response->assertSee('switchClassModalLang', false);
        $response->assertSee('switchGradeModalLang', false);
    }

    public function test_school_classes_grades_ajax_tab_switching_returns_partial_html(): void
    {
        app(\Database\Seeders\SchoolClassAndGradeSeeder::class)->run();

        // 1. Switch to Grades Tab via AJAX
        $response = $this->actingAs($this->schoolAdmin)
            ->getJson('/school/classes-grades?tab=grades', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'activeTab' => 'grades',
        ]);
        $this->assertNotEmpty($response->json('html'));

        // 2. Switch to Rooms Tab via AJAX
        $roomsResponse = $this->actingAs($this->schoolAdmin)
            ->getJson('/school/classes-grades?tab=rooms', ['X-Requested-With' => 'XMLHttpRequest']);

        $roomsResponse->assertStatus(200);
        $roomsResponse->assertJson([
            'success' => true,
            'activeTab' => 'rooms',
        ]);
        $this->assertNotEmpty($roomsResponse->json('html'));
    }

    public function test_school_admin_can_store_and_update_class_with_multilingual_database_support(): void
    {
        $grade = SchoolGrade::firstOrCreate(
            ['school_id' => $this->school->id, 'level_number' => 7],
            [
                'code' => 'GR-07',
                'name' => 'Grade 7',
                'name_km' => 'ថ្នាក់ទី ៧',
                'academic_stage' => 'Lower Secondary',
                'academic_stage_km' => 'អនុវិទ្យាល័យ',
                'status' => 'active',
            ]
        );

        $shift = SchoolShift::where('school_id', $this->school->id)->first();

        // 1. Create multilingual class via POST
        $createPayload = [
            'code' => '7A',
            'name' => 'Class 7A',
            'name_km' => 'ថ្នាក់ទី ៧A',
            'school_grade_id' => $grade->id,
            'school_shift_id' => $shift?->id,
            'room_number' => 'Building C - Rm 302',
            'room_number_km' => 'អគារ C - បន្ទប់ ៣០២',
            'teacher_name' => 'Dr. Chea Roth',
            'teacher_name_km' => 'បណ្ឌិត ជា រដ្ឋ',
            'teacher_phone' => '+855 12 777 888',
            'capacity' => 42,
            'status' => 'active',
            'description' => 'Advanced STEM class',
            'description_km' => 'ថ្នាក់រៀនឯកទេស STEM កម្រិតខ្ពស់',
        ];

        $postResponse = $this->actingAs($this->schoolAdmin)
            ->postJson('/school/classes', $createPayload);

        $postResponse->assertStatus(201);
        $postResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('school_classes', [
            'code' => '7A',
            'name' => 'Class 7A',
            'name_km' => 'ថ្នាក់ទី ៧A',
            'room_number' => 'Building C - Rm 302',
            'room_number_km' => 'អគារ C - បន្ទប់ ៣០២',
            'teacher_name' => 'Dr. Chea Roth',
            'teacher_name_km' => 'បណ្ឌិត ជា រដ្ឋ',
            'capacity' => 42,
        ]);

        $createdClass = SchoolClass::where('code', '7A')->first();
        $this->assertNotNull($createdClass);

        // 2. Update multilingual class via PUT
        $updatePayload = [
            'code' => '7A',
            'name' => 'Class 7A Advanced',
            'name_km' => 'ថ្នាក់ទី ៧A កម្រិតខ្ពស់',
            'school_grade_id' => $grade->id,
            'school_shift_id' => $shift?->id,
            'room_number' => 'Building C - Rm 305',
            'room_number_km' => 'អគារ C - បន្ទប់ ៣០៥',
            'teacher_name' => 'Dr. Chea Roth Ph.D',
            'teacher_name_km' => 'បណ្ឌិត ជា រដ្ឋ បរិញ្ញាបត្រជាន់ខ្ពស់',
            'teacher_phone' => '+855 12 777 999',
            'capacity' => 45,
            'status' => 'active',
            'description' => 'Updated syllabus',
            'description_km' => 'កម្មវិធីសិក្សាថ្មី',
        ];

        $putResponse = $this->actingAs($this->schoolAdmin)
            ->putJson("/school/classes/{$createdClass->id}", $updatePayload);

        $putResponse->assertStatus(200);
        $putResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('school_classes', [
            'id' => $createdClass->id,
            'name' => 'Class 7A Advanced',
            'name_km' => 'ថ្នាក់ទី ៧A កម្រិតខ្ពស់',
            'capacity' => 45,
        ]);

        // 3. Delete class via DELETE
        $delResponse = $this->actingAs($this->schoolAdmin)
            ->deleteJson("/school/classes/{$createdClass->id}");

        $delResponse->assertStatus(200);
        $delResponse->assertJson(['success' => true]);

        $this->assertSoftDeleted('school_classes', [
            'id' => $createdClass->id,
        ]);
    }

    public function test_school_admin_can_store_and_update_grade_with_multilingual_database_support(): void
    {
        // 1. Create multilingual grade via POST
        $createPayload = [
            'code' => 'GR-08',
            'name' => 'Grade 8',
            'name_km' => 'ថ្នាក់ទី ៨',
            'level_number' => 8,
            'academic_stage' => 'Lower Secondary',
            'academic_stage_km' => 'អនុវិទ្យាល័យ',
            'status' => 'active',
            'description' => 'Eighth grade curriculum',
            'description_km' => 'កម្មវិធីសិក្សាថ្នាក់ទី ៨',
        ];

        $postResponse = $this->actingAs($this->schoolAdmin)
            ->postJson('/school/grades', $createPayload);

        $postResponse->assertStatus(201);
        $postResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('school_grades', [
            'code' => 'GR-08',
            'name' => 'Grade 8',
            'name_km' => 'ថ្នាក់ទី ៨',
            'academic_stage' => 'Lower Secondary',
            'academic_stage_km' => 'អនុវិទ្យាល័យ',
            'level_number' => 8,
        ]);

        $grade = SchoolGrade::where('code', 'GR-08')->first();
        $this->assertNotNull($grade);

        // 2. Update multilingual grade via PUT
        $updatePayload = [
            'code' => 'GR-08',
            'name' => 'Grade 8 Standard',
            'name_km' => 'ថ្នាក់ទី ៨ ស្តង់ដារ',
            'level_number' => 8,
            'academic_stage' => 'Middle School',
            'academic_stage_km' => 'អនុវិទ្យាល័យកណ្តាល',
            'status' => 'active',
            'description' => 'Updated Grade 8',
            'description_km' => 'កែប្រែកម្មវិធីសិក្សាថ្នាក់ទី ៨',
        ];

        $putResponse = $this->actingAs($this->schoolAdmin)
            ->putJson("/school/grades/{$grade->id}", $updatePayload);

        $putResponse->assertStatus(200);
        $putResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('school_grades', [
            'id' => $grade->id,
            'name' => 'Grade 8 Standard',
            'name_km' => 'ថ្នាក់ទី ៨ ស្តង់ដារ',
            'academic_stage' => 'Middle School',
        ]);

        // 3. Delete grade via DELETE
        $delResponse = $this->actingAs($this->schoolAdmin)
            ->deleteJson("/school/grades/{$grade->id}");

        $delResponse->assertStatus(200);
        $delResponse->assertJson(['success' => true]);

        $this->assertSoftDeleted('school_grades', [
            'id' => $grade->id,
        ]);
    }
}

