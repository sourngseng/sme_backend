<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\School;
use App\Models\SchoolShift;
use App\Models\Student;
use App\Models\StudentNutritionProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Ensure primary schools exist
            $ppaSchool = School::firstOrCreate(
                ['code' => 'PPA-001'],
                [
                    'name' => 'Phnom Penh International Academy',
                    'level' => 'primary',
                    'status' => 'active',
                    'phone' => '+85523888001',
                    'email' => 'info@pp-academy.edu.kh',
                    'address' => 'No. 128 Norodom Blvd, Boeung Keng Kang, Phnom Penh',
                    'latitude' => 11.5564000,
                    'longitude' => 104.9282000,
                ]
            );

            $watBoSchool = School::firstOrCreate(
                ['code' => 'WAT-001'],
                [
                    'name' => 'Wat Bo Primary School',
                    'level' => 'primary',
                    'status' => 'active',
                    'phone' => '+855 63 963 888',
                    'email' => 'admin@watbo-school.edu.kh',
                    'address' => 'Wat Bo Village, Salakamreuk, Siem Reap',
                    'latitude' => 13.3542000,
                    'longitude' => 103.8617000,
                ]
            );

            $this->seedStudentsForSchool($ppaSchool);
            $this->seedStudentsForSchool($watBoSchool);
        });
    }

    /**
     * Seed comprehensive students for a given school.
     */
    protected function seedStudentsForSchool(School $school): void
    {
        // Ensure shifts exist
        $morningShift = SchoolShift::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Morning Shift'],
            ['start_time' => '07:30:00', 'end_time' => '11:30:00', 'status' => 'active']
        );
        $lunchShift = SchoolShift::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Lunch & Afternoon Shift'],
            ['start_time' => '11:30:00', 'end_time' => '16:30:00', 'status' => 'active']
        );

        $defaultPassword = bcrypt('password123');

        // Master reference student dataset (Image 2 exact records + extended pool)
        $studentsData = [
            [
                'code' => 'STU0001',
                'name_en' => 'Sokha Piseth',
                'name_km' => 'សុខា ពិសិដ្ឋ',
                'avatar' => 'https://images.unsplash.com/photo-1544717302-de2939b7ef71?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2016-04-12',
                'grade' => 'Grade 4',
                'class' => '4A',
                'parent_name' => 'Sokha Rithy',
                'parent_phone' => '+855 12 345 678',
                'parent_email' => 'sokha.rithy@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0002',
                'name_en' => 'Chanthy Sreyneang',
                'name_km' => 'ចាន់ធី ស្រីនាង',
                'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2016-08-25',
                'grade' => 'Grade 4',
                'class' => '4A',
                'parent_name' => 'Chanthy Dara',
                'parent_phone' => '+855 16 234 567',
                'parent_email' => 'chanthy.dara@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => ['Egg'],
                'status' => 'active',
            ],
            [
                'code' => 'STU0003',
                'name_en' => 'Dara Vuthy',
                'name_km' => 'ដារ៉ា វុទ្ធី',
                'avatar' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2015-11-03',
                'grade' => 'Grade 5',
                'class' => '5B',
                'parent_name' => 'Dara Sopheak',
                'parent_phone' => '+855 12 987 654',
                'parent_email' => 'dara.sopheak@parent.test',
                'meal_plan' => 'Halal Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0004',
                'name_en' => 'Leaksmey Rathana',
                'name_km' => 'លក្ខិណា រតនា',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2014-06-19',
                'grade' => 'Grade 6',
                'class' => '6A',
                'parent_name' => 'Rathana Chenda',
                'parent_phone' => '+855 17 456 789',
                'parent_email' => 'rathana.chenda@parent.test',
                'meal_plan' => 'Vegetarian',
                'allergies' => ['Peanut'],
                'status' => 'active',
            ],
            [
                'code' => 'STU0005',
                'name_en' => 'Sovann Reach',
                'name_km' => 'សុវណ្ណ រាជ',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2016-09-14',
                'grade' => 'Grade 4',
                'class' => '4B',
                'parent_name' => 'Reach Sreyleak',
                'parent_phone' => '+855 93 123 456',
                'parent_email' => 'reach.sreyleak@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'inactive',
            ],
            [
                'code' => 'STU0006',
                'name_en' => 'Chenda Malis',
                'name_km' => 'ចិន្តា ម៉ាលីស',
                'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2017-02-18',
                'grade' => 'Grade 3',
                'class' => '3A',
                'parent_name' => 'Malis Kimheng',
                'parent_phone' => '+855 88 765 432',
                'parent_email' => 'malis.kimheng@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0007',
                'name_en' => 'Kosal Vichea',
                'name_km' => 'កុសល វិជ្ជា',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2015-05-30',
                'grade' => 'Grade 5',
                'class' => '5A',
                'parent_name' => 'Vichea Somaly',
                'parent_phone' => '+855 11 222 333',
                'parent_email' => 'vichea.somaly@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0008',
                'name_en' => 'Ravy Sopheak',
                'name_km' => 'រ៉ាវី សុភ័ក្ត្រ',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2018-07-22',
                'grade' => 'Grade 2',
                'class' => '2A',
                'parent_name' => 'Sopheak Kanika',
                'parent_phone' => '+855 97 555 888',
                'parent_email' => 'sopheak.kanika@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => ['Milk'],
                'status' => 'allergy_alert',
            ],
            [
                'code' => 'STU0009',
                'name_en' => 'Pich Monyroth',
                'name_km' => 'ពេជ្រ មុនីរ័ត្ន',
                'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2019-01-10',
                'grade' => 'Grade 1',
                'class' => '1A',
                'parent_name' => 'Monyroth Sovan',
                'parent_phone' => '+855 96 111 444',
                'parent_email' => 'monyroth.sovan@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0010',
                'name_en' => 'Nary Kimheng',
                'name_km' => 'ណារី គីមហេង',
                'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2017-10-05',
                'grade' => 'Grade 3',
                'class' => '3B',
                'parent_name' => 'Kimheng Narith',
                'parent_phone' => '+855 15 999 000',
                'parent_email' => 'kimheng.narith@parent.test',
                'meal_plan' => 'Halal Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            // Extended Pool (STU0011 to STU0045) for true server-side pagination & filtering
            [
                'code' => 'STU0011',
                'name_en' => 'Kong Vannak',
                'name_km' => 'គង់ វណ្ណៈ',
                'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2016-03-12',
                'grade' => 'Grade 4',
                'class' => '4A',
                'parent_name' => 'Kong Sambath',
                'parent_phone' => '+855 12 777 888',
                'parent_email' => 'kong.sambath@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0012',
                'name_en' => 'Heng Sreypov',
                'name_km' => 'ហេង ស្រីពៅ',
                'avatar' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2015-12-04',
                'grade' => 'Grade 5',
                'class' => '5A',
                'parent_name' => 'Heng Channa',
                'parent_phone' => '+855 16 888 999',
                'parent_email' => 'heng.channa@parent.test',
                'meal_plan' => 'Vegetarian',
                'allergies' => ['Seafood'],
                'status' => 'allergy_alert',
            ],
            [
                'code' => 'STU0013',
                'name_en' => 'Tieng Sambath',
                'name_km' => 'ទៀង សម្បត្តិ',
                'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2014-08-14',
                'grade' => 'Grade 6',
                'class' => '6A',
                'parent_name' => 'Tieng Vandy',
                'parent_phone' => '+855 92 333 444',
                'parent_email' => 'tieng.vandy@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0014',
                'name_en' => 'Sorn Vibol',
                'name_km' => 'ស៊ន វិបុល',
                'avatar' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2017-05-18',
                'grade' => 'Grade 3',
                'class' => '3A',
                'parent_name' => 'Sorn Bopha',
                'parent_phone' => '+855 81 222 111',
                'parent_email' => 'sorn.bopha@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0015',
                'name_en' => 'Keo Bopha',
                'name_km' => 'កែវ បុប្ផា',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2018-11-20',
                'grade' => 'Grade 2',
                'class' => '2A',
                'parent_name' => 'Keo Sreynich',
                'parent_phone' => '+855 77 444 555',
                'parent_email' => 'keo.sreynich@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => ['Egg'],
                'status' => 'active',
            ],
            [
                'code' => 'STU0016',
                'name_en' => 'Chan Piseth',
                'name_km' => 'ចាន់ ពិសិដ្ឋ',
                'avatar' => 'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2019-03-08',
                'grade' => 'Grade 1',
                'class' => '1A',
                'parent_name' => 'Chan Sopheap',
                'parent_phone' => '+855 12 111 222',
                'parent_email' => 'chan.sopheap@parent.test',
                'meal_plan' => 'Halal Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0017',
                'name_en' => 'Ly Sinat',
                'name_km' => 'លី ស៊ីណាត',
                'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2016-01-28',
                'grade' => 'Grade 4',
                'class' => '4B',
                'parent_name' => 'Ly Rith',
                'parent_phone' => '+855 15 333 777',
                'parent_email' => 'ly.rith@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => ['Peanut', 'Soy'],
                'status' => 'allergy_alert',
            ],
            [
                'code' => 'STU0018',
                'name_en' => 'Ouk Rithy',
                'name_km' => 'អ៊ុក រិទ្ធី',
                'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2015-09-17',
                'grade' => 'Grade 5',
                'class' => '5B',
                'parent_name' => 'Ouk Visal',
                'parent_phone' => '+855 93 444 888',
                'parent_email' => 'ouk.visal@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0019',
                'name_en' => 'Meas Kunthea',
                'name_km' => 'មាស គន្ធា',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2014-04-03',
                'grade' => 'Grade 6',
                'class' => '6A',
                'parent_name' => 'Meas Sokun',
                'parent_phone' => '+855 88 123 789',
                'parent_email' => 'meas.sokun@parent.test',
                'meal_plan' => 'Vegetarian',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0020',
                'name_en' => 'Phon Sovannarith',
                'name_km' => 'ផុន សុវណ្ណារិទ្ធ',
                'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2017-08-11',
                'grade' => 'Grade 3',
                'class' => '3B',
                'parent_name' => 'Phon Chantha',
                'parent_phone' => '+855 97 999 111',
                'parent_email' => 'phon.chantha@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'inactive',
            ],
            [
                'code' => 'STU0021',
                'name_en' => 'Ros Sreymom',
                'name_km' => 'រស់ ស្រីមុំ',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2018-02-14',
                'grade' => 'Grade 2',
                'class' => '2A',
                'parent_name' => 'Ros Samnang',
                'parent_phone' => '+855 12 888 222',
                'parent_email' => 'ros.samnang@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0022',
                'name_en' => 'Som Bunthan',
                'name_km' => 'សោម ប៊ុនថន',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2019-06-21',
                'grade' => 'Grade 1',
                'class' => '1A',
                'parent_name' => 'Som Chanthy',
                'parent_phone' => '+855 16 555 444',
                'parent_email' => 'som.chanthy@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0023',
                'name_en' => 'Ung Sreypich',
                'name_km' => 'អ៊ុង ស្រីពេជ្រ',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2016-10-15',
                'grade' => 'Grade 4',
                'class' => '4A',
                'parent_name' => 'Ung Bunly',
                'parent_phone' => '+855 92 111 999',
                'parent_email' => 'ung.bunly@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => ['Milk'],
                'status' => 'active',
            ],
            [
                'code' => 'STU0024',
                'name_en' => 'Vannak Panha',
                'name_km' => 'វណ្ណៈ បញ្ញា',
                'avatar' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2015-03-29',
                'grade' => 'Grade 5',
                'class' => '5A',
                'parent_name' => 'Vannak Sarin',
                'parent_phone' => '+855 11 999 888',
                'parent_email' => 'vannak.sarin@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0025',
                'name_en' => 'You Sokunthea',
                'name_km' => 'យូ សុគន្ធា',
                'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2014-12-07',
                'grade' => 'Grade 6',
                'class' => '6A',
                'parent_name' => 'You Dara',
                'parent_phone' => '+855 17 222 333',
                'parent_email' => 'you.dara@parent.test',
                'meal_plan' => 'Halal Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0026',
                'name_en' => 'Chum Seyha',
                'name_km' => 'ជុំ សីហា',
                'avatar' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2017-04-09',
                'grade' => 'Grade 3',
                'class' => '3A',
                'parent_name' => 'Chum Vuth',
                'parent_phone' => '+855 93 777 666',
                'parent_email' => 'chum.vuth@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0027',
                'name_en' => 'Eam Channary',
                'name_km' => 'អ៊ាម ចាន់ណារី',
                'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2018-09-12',
                'grade' => 'Grade 2',
                'class' => '2A',
                'parent_name' => 'Eam Sophat',
                'parent_phone' => '+855 88 444 333',
                'parent_email' => 'eam.sophat@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0028',
                'name_en' => 'Khat Visal',
                'name_km' => 'ខាត់ វិសាល',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2019-11-23',
                'grade' => 'Grade 1',
                'class' => '1A',
                'parent_name' => 'Khat Sokhom',
                'parent_phone' => '+855 96 888 777',
                'parent_email' => 'khat.sokhom@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
            [
                'code' => 'STU0029',
                'name_en' => 'Mom Sreyoun',
                'name_km' => 'មុំ ស្រីអូន',
                'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'female',
                'dob' => '2016-07-04',
                'grade' => 'Grade 4',
                'class' => '4B',
                'parent_name' => 'Mom Chanthou',
                'parent_phone' => '+855 15 222 666',
                'parent_email' => 'mom.chanthou@parent.test',
                'meal_plan' => 'Vegetarian',
                'allergies' => ['Egg'],
                'status' => 'allergy_alert',
            ],
            [
                'code' => 'STU0030',
                'name_en' => 'Net Chetra',
                'name_km' => 'ណែត ចិត្រា',
                'avatar' => 'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=100&h=100&fit=crop&crop=faces',
                'gender' => 'male',
                'dob' => '2015-01-19',
                'grade' => 'Grade 5',
                'class' => '5B',
                'parent_name' => 'Net Kosal',
                'parent_phone' => '+855 12 666 555',
                'parent_email' => 'net.kosal@parent.test',
                'meal_plan' => 'Regular Meal',
                'allergies' => [],
                'status' => 'active',
            ],
        ];

        foreach ($studentsData as $item) {
            // Check or create Guardian user
            $parentUser = User::firstOrCreate(
                ['email' => $item['parent_email']],
                [
                    'full_name' => $item['parent_name'],
                    'phone' => $item['parent_phone'],
                    'password' => $defaultPassword,
                    'status' => 'active',
                    'role' => 'parent',
                ]
            );

            $guardian = Guardian::firstOrCreate(
                ['user_id' => $parentUser->id],
                ['relationship' => $item['gender'] === 'male' ? 'Father' : 'Mother']
            );

            // Unique student code across schools (WAT-001 gets STU0001-STU0030 matching Image 2 & tests)
            $prefix = $school->code === 'WAT-001' ? 'STU' : 'PPA';
            $numPart = substr($item['code'], 3); // e.g. '0001'
            $code = $prefix . $numPart;

            $shift = (str_contains($item['grade'], '1') || str_contains($item['grade'], '2')) ? $morningShift : $lunchShift;

            $student = Student::updateOrCreate(
                ['student_code' => $code],
                [
                    'school_id' => $school->id,
                    'school_shift_id' => $shift->id,
                    'full_name' => $item['name_en'],
                    'khmer_name' => $item['name_km'],
                    'date_of_birth' => $item['dob'],
                    'gender' => $item['gender'],
                    'avatar_url' => $item['avatar'],
                    'grade_level' => $item['grade'],
                    'class_name' => $item['class'],
                    'status' => $item['status'],
                ]
            );

            // Attach Guardian
            $guardian->students()->syncWithoutDetaching([
                $student->id => [
                    'is_primary' => true,
                    'can_order' => true,
                    'can_view_nutrition' => true,
                ],
            ]);

            // Update Nutrition Profile
            StudentNutritionProfile::updateOrCreate(
                ['student_id' => $student->id],
                [
                    'dietary_preferences' => [$item['meal_plan']],
                    'allergen_ids' => $item['allergies'],
                    'notes' => count($item['allergies']) > 0 ? 'Medical allergy: ' . implode(', ', $item['allergies']) : null,
                ]
            );
        }
    }
}
