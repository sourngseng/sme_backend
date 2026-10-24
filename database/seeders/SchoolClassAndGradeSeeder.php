<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolGrade;
use App\Models\SchoolShift;
use Illuminate\Database\Seeder;

class SchoolClassAndGradeSeeder extends Seeder
{
    /**
     * Run the database seeds for multilingual grades and classes.
     */
    public function run(): void
    {
        $schools = School::all();

        foreach ($schools as $school) {
            $morningShift = SchoolShift::where('school_id', $school->id)->where('name', 'like', '%Morning%')->first()
                ?? SchoolShift::where('school_id', $school->id)->first();
            $lunchShift = SchoolShift::where('school_id', $school->id)->where('name', 'like', '%Lunch%')->first()
                ?? $morningShift;

            // 1. Seed 6 Grades (Grades 1 to 6)
            $gradesData = [
                ['code' => 'G1', 'name' => 'Grade 1', 'name_km' => 'ថ្នាក់ទី ១', 'level' => 1, 'stage' => 'Early Primary', 'stage_km' => 'បឋមសិក្សាដំបូង', 'desc' => 'Foundational primary education & introductory nutrition literacy', 'desc_km' => 'ការអប់រំបឋមសិក្សាមូលដ្ឋាន និងអាហារូបត្ថម្ភដំបូង'],
                ['code' => 'G2', 'name' => 'Grade 2', 'name_km' => 'ថ្នាក់ទី ២', 'level' => 2, 'stage' => 'Early Primary', 'stage_km' => 'បឋមសិក្សាដំបូង', 'desc' => 'Standard primary level with dietary habit monitoring', 'desc_km' => 'កម្រិតបឋមសិក្សាស្តង់ដារ និងការតាមដានទម្លាប់ទទួលទានអាហារ'],
                ['code' => 'G3', 'name' => 'Grade 3', 'name_km' => 'ថ្នាក់ទី ៣', 'level' => 3, 'stage' => 'Early Primary', 'stage_km' => 'បឋមសិក្សាដំបូង', 'desc' => 'Intermediate primary transition cohort', 'desc_km' => 'កម្រិតបឋមសិក្សាអន្តរកាល'],
                ['code' => 'G4', 'name' => 'Grade 4', 'name_km' => 'ថ្នាក់ទី ៤', 'level' => 4, 'stage' => 'Upper Primary', 'stage_km' => 'បឋមសិក្សាជាន់ខ្ពស់', 'desc' => 'Active meal tracking with allergen protection badges', 'desc_km' => 'ការតាមដានរបបអាហារសកម្មជាមួយផ្លាកសញ្ញាអាឡែរហ្ស៊ី'],
                ['code' => 'G5', 'name' => 'Grade 5', 'name_km' => 'ថ្នាក់ទី ៥', 'level' => 5, 'stage' => 'Upper Primary', 'stage_km' => 'បឋមសិក្សាជាន់ខ្ពស់', 'desc' => 'High participation meal rosters & sports nutrition options', 'desc_km' => 'បញ្ជីមុខម្ហូបអាហារូបត្ថម្ភ និងជម្រើសសម្រាប់កីឡា'],
                ['code' => 'G6', 'name' => 'Grade 6', 'name_km' => 'ថ្នាក់ទី ៦', 'level' => 6, 'stage' => 'Upper Primary', 'stage_km' => 'បឋមសិក្សាជាន់ខ្ពស់', 'desc' => 'Graduating primary cohort with pre-ordered healthy lunchboxes', 'desc_km' => 'សិស្សបញ្ចប់បឋមសិក្សា ជាមួយប្រអប់បាយសុខភាព'],
            ];

            $gradeModels = [];
            foreach ($gradesData as $g) {
                $gradeModels[$g['name']] = SchoolGrade::firstOrCreate(
                    ['school_id' => $school->id, 'code' => $g['code']],
                    [
                        'name' => $g['name'],
                        'name_km' => $g['name_km'],
                        'level_number' => $g['level'],
                        'academic_stage' => $g['stage'],
                        'academic_stage_km' => $g['stage_km'],
                        'description' => $g['desc'],
                        'description_km' => $g['desc_km'],
                        'status' => 'active',
                    ]
                );
            }

            // 2. Seed Classrooms (Homerooms)
            $classesData = [
                ['grade' => 'Grade 1', 'code' => '1A', 'name' => 'Class 1A', 'name_km' => 'ថ្នាក់ ១A', 'room' => 'Room 101 - Building A', 'room_km' => 'បន្ទប់ ១០១ - អគារ A', 'teacher' => 'Sokha Rithy', 'teacher_km' => 'សុខា រិទ្ធី', 'phone' => '+855 12 345 678', 'cap' => 40, 'shift_id' => $morningShift?->id],
                ['grade' => 'Grade 1', 'code' => '1B', 'name' => 'Class 1B', 'name_km' => 'ថ្នាក់ ១B', 'room' => 'Room 102 - Building A', 'room_km' => 'បន្ទប់ ១០២ - អគារ A', 'teacher' => 'Leakena Som', 'teacher_km' => 'លក្ខិណា សំ', 'phone' => '+855 12 888 102', 'cap' => 40, 'shift_id' => $morningShift?->id],
                ['grade' => 'Grade 2', 'code' => '2A', 'name' => 'Class 2A', 'name_km' => 'ថ្នាក់ ២A', 'room' => 'Room 103 - Building A', 'room_km' => 'បន្ទប់ ១០៣ - អគារ A', 'teacher' => 'Sovann Reach', 'teacher_km' => 'សុវណ្ណ រាជ', 'phone' => '+855 70 555 201', 'cap' => 40, 'shift_id' => $morningShift?->id],
                ['grade' => 'Grade 2', 'code' => '2B', 'name' => 'Class 2B', 'name_km' => 'ថ្នាក់ ២B', 'room' => 'Room 104 - Building A', 'room_km' => 'បន្ទប់ ១០៤ - អគារ A', 'teacher' => 'Malis Chenda', 'teacher_km' => 'ម្លិះ ចិន្តា', 'phone' => '+855 89 222 202', 'cap' => 38, 'shift_id' => $morningShift?->id],
                ['grade' => 'Grade 3', 'code' => '3A', 'name' => 'Class 3A', 'name_km' => 'ថ្នាក់ ៣A', 'room' => 'Room 201 - Building B', 'room_km' => 'បន្ទប់ ២០១ - អគារ B', 'teacher' => 'Pisey Vanna', 'teacher_km' => 'ពិសី វណ្ណា', 'phone' => '+855 92 111 301', 'cap' => 38, 'shift_id' => $lunchShift?->id],
                ['grade' => 'Grade 3', 'code' => '3B', 'name' => 'Class 3B', 'name_km' => 'ថ្នាក់ ៣B', 'room' => 'Room 202 - Building B', 'room_km' => 'បន្ទប់ ២០២ - អគារ B', 'teacher' => 'Kimheng Narith', 'teacher_km' => 'គីមហេង ណារិទ្ធ', 'phone' => '+855 93 444 302', 'cap' => 38, 'shift_id' => $lunchShift?->id],
                ['grade' => 'Grade 4', 'code' => '4A', 'name' => 'Class 4A', 'name_km' => 'ថ្នាក់ ៤A', 'room' => 'Room 203 - Building B', 'room_km' => 'បន្ទប់ ២០៣ - អគារ B', 'teacher' => 'Chanthy Dara', 'teacher_km' => 'ចាន់ធី ដារ៉ា', 'phone' => '+855 16 234 567', 'cap' => 42, 'shift_id' => $morningShift?->id],
                ['grade' => 'Grade 4', 'code' => '4B', 'name' => 'Class 4B', 'name_km' => 'ថ្នាក់ ៤B', 'room' => 'Room 204 - Building B', 'room_km' => 'បន្ទប់ ២០៤ - អគារ B', 'teacher' => 'Reach Sreyleak', 'teacher_km' => 'រាជ ស្រីលក្ខណ៍', 'phone' => '+855 17 999 402', 'cap' => 40, 'shift_id' => $lunchShift?->id],
                ['grade' => 'Grade 5', 'code' => '5A', 'name' => 'Class 5A', 'name_km' => 'ថ្នាក់ ៥A', 'room' => 'Room 301 - Building C', 'room_km' => 'បន្ទប់ ៣០១ - អគារ C', 'teacher' => 'Vichea Somaly', 'teacher_km' => 'វិជ្ជា សោម៉ាលី', 'phone' => '+855 78 333 501', 'cap' => 42, 'shift_id' => $morningShift?->id],
                ['grade' => 'Grade 5', 'code' => '5B', 'name' => 'Class 5B', 'name_km' => 'ថ្នាក់ ៥B', 'room' => 'Room 302 - Building C', 'room_km' => 'បន្ទប់ ៣០២ - អគារ C', 'teacher' => 'Dara Sopheak', 'teacher_km' => 'ដារ៉ា សុភ័ក្រ្ត', 'phone' => '+855 12 987 654', 'cap' => 40, 'shift_id' => $lunchShift?->id],
                ['grade' => 'Grade 6', 'code' => '6A', 'name' => 'Class 6A', 'name_km' => 'ថ្នាក់ ៦A', 'room' => 'Room 303 - Building C', 'room_km' => 'បន្ទប់ ៣០៣ - អគារ C', 'teacher' => 'Rathana Chenda', 'teacher_km' => 'រតនា ចិន្តា', 'phone' => '+855 15 777 601', 'cap' => 42, 'shift_id' => $morningShift?->id],
                ['grade' => 'Grade 6', 'code' => '6B', 'name' => 'Class 6B', 'name_km' => 'ថ្នាក់ ៦B', 'room' => 'Room 304 - Building C', 'room_km' => 'បន្ទប់ ៣០៤ - អគារ C', 'teacher' => 'Kosal Phanna', 'teacher_km' => 'កុសល ផាន់ណា', 'phone' => '+855 81 666 602', 'cap' => 40, 'shift_id' => $lunchShift?->id],
            ];

            foreach ($classesData as $c) {
                $gradeModel = $gradeModels[$c['grade']] ?? null;
                SchoolClass::firstOrCreate(
                    ['school_id' => $school->id, 'code' => $c['code']],
                    [
                        'school_grade_id' => $gradeModel?->id,
                        'name' => $c['name'],
                        'name_km' => $c['name_km'],
                        'room_number' => $c['room'],
                        'room_number_km' => $c['room_km'],
                        'teacher_name' => $c['teacher'],
                        'teacher_name_km' => $c['teacher_km'],
                        'teacher_phone' => $c['phone'],
                        'capacity' => $c['cap'],
                        'school_shift_id' => $c['shift_id'],
                        'description' => 'Homeroom class for ' . $c['grade'] . ' students.',
                        'description_km' => 'ថ្នាក់បន្ទប់សម្រាប់សិស្ស ' . ($gradeModel?->name_km ?? $c['grade']) . '។',
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
