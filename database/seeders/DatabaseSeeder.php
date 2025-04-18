<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // hero and social modia seeder
        $this->call(HeroesSeeder::class);
        $this->call(SocialMediaSeeder::class);

        // user seeder
        $this->call(UserSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(TeacherSeeder::class);

        // master data seeder
        $this->call(AcademicYearSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(ClassSeeder::class);
        $this->call(StudentClassSeeder::class);

        
        // schedule seeder
        $this->call(SchedulesTimeSeeder::class);
    }
}
