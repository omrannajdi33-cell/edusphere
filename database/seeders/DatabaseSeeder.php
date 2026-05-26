<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SubjectSeeder::class,
            AdminUserSeeder::class,
            ActivityDemoSeeder::class,
            PointBehaviorSeeder::class,
        ]);
    }
}
