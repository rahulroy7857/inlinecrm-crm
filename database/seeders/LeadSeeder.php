<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\LeadContactLog;
use App\Models\LeadEducation;
use App\Models\LeadExam;
use App\Models\LeadPayment;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, truncate all related tables to avoid duplicate entries
        LeadExam::truncate();
        LeadEducation::truncate();
        LeadPayment::truncate();
        LeadContactLog::truncate();
        Lead::truncate();

        $faker = Faker::create('en_IN'); // Using Indian locale for more relevant data

        for ($i = 0; $i < 20; $i++) {
            $gender = $faker->randomElement(['Male', 'Female']);
            $firstName = $gender === 'Male' ? $faker->firstNameMale : $faker->firstNameFemale;

             // Generate lead_id
            $leadId = 'LEAD' . str_pad($i + 1, 5, '0', STR_PAD_LEFT);

            $lead = Lead::create([
                'name' => $firstName . ' ' . $faker->lastName,
                'lead_id' => $leadId,
                'status' => $faker->randomElement(['Hot', 'Warm', 'Cold']),
                'source_id' => rand(1, 5), // Assuming you have sources seeded
                'counselor_id' => rand(1, 5), // Assuming you have counselors seeded
                'academic_year_id' => rand(1, 3), // Assuming you have academic years seeded
                'course_id' => rand(1, 10), // Assuming you have courses seeded
                'specialization' => $faker->randomElement(['Computer Science', 'Mechanical', 'Civil', 'Electronics', 'Chemical']),
                'college_id' => rand(1, 5), // Assuming you have colleges seeded
                'country' => 'India',
                'state' => $faker->state,
                'father_name' => $faker->name('male'),
                'father_occupation' => $faker->jobTitle,
                'mother_name' => $faker->name('female'),
                'mother_occupation' => $faker->jobTitle,
                'guardian_name' => $faker->name,
                'relation' => $faker->randomElement(['Uncle', 'Aunt', 'Grandfather', 'Grandmother']),
                'gender' => $gender,
                'dob' => $faker->date('Y-m-d', '-17 years'),
                'aadhar' => rand(100000000000, 999999999999),
                'notes' => $faker->sentence(10),
                'mobile' => $faker->numerify('##########'),
                'alternative_mobile' => $faker->numerify('##########'),
                'father_mobile' => $faker->numerify('##########'),
                'mother_mobile' => $faker->numerify('##########'),
                'guardian_mobile' => $faker->numerify('##########'),
                'personal_email' => $faker->email,
                'father_email' => $faker->email,
                'mother_email' => $faker->email,
                'guardian_email' => $faker->email,
                'present_address' => $faker->address,
                'present_state' => $faker->state,
                'present_city' => $faker->city,
                'present_place' => $faker->streetName,
                'present_pin' => $faker->postcode,
                'permanent_address' => $faker->address,
                'permanent_state' => $faker->state,
                'permanent_city' => $faker->city,
                'permanent_place' => $faker->streetName,
                'permanent_pin' => $faker->postcode,
            ]);

            // Add 3-5 contact logs for each lead
            $contactCount = rand(3, 5);
            for ($j = 0; $j < $contactCount; $j++) {
                LeadContactLog::create([
                    'lead_id' => $lead->id,
                    'contact_date' => $faker->dateTimeBetween('-3 months', 'now'),
                    'remark' => $faker->sentence(8),
                    'duration' => rand(5, 30),
                    'type' => $faker->randomElement(['Call', 'Email', 'SMS', 'WhatsApp', 'In-Person']),
                    'contacted_by' => rand(1, 5), // Assuming 5 admins exist
                ]);
            }

            // Add 2-3 payments for each lead
            $paymentCount = rand(2, 3);
            for ($j = 0; $j < $paymentCount; $j++) {
                LeadPayment::create([
                    'lead_id' => $lead->id,
                    'payment_date' => $faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
                    'payment_type' => $faker->randomElement(['Application', 'Registration', 'Admission']),
                    'payment_mode' => $faker->randomElement(['Cash', 'Card', 'UPI', 'Bank Transfer']),
                    'amount' => $faker->randomFloat(2, 1000, 50000),
                    'remark' => $faker->sentence(4),
                ]);
            }

            // Add education history (2-3 records)
            $qualifications = [
                ['10th', '2019'],
                ['12th', '2021'],
                ['Bachelor', '2024']
            ];

            foreach ($qualifications as $qualification) {
                LeadEducation::create([
                    'lead_id' => $lead->id,
                    'qualification' => $qualification[0],
                    'marks' => rand(60, 98) . '%',
                    'institute' => $faker->company . ' School',
                    'year' => $qualification[1],
                ]);
            }

            // Add 1-2 entrance exams
            $examCount = rand(1, 2);
            $exams = ['JEE Main', 'NEET', 'KCET', 'COMEDK'];
            $selectedExams = $faker->randomElements($exams, $examCount);
            
            foreach ($selectedExams as $exam) {
                LeadExam::create([
                    'lead_id' => $lead->id,
                    'exam_name' => $exam,
                    'score' => rand(5000, 50000),
                    'year' => rand(2023, 2024),
                    'remarks' => $faker->sentence(3),
                ]);
            }
        }
    }
}
