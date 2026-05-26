<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@edusphere.local'],
            [
                'name' => 'Professeur',
                'username' => 'prof',
                'password' => Hash::make('prof123'),
                'role' => UserRole::Admin,
                'is_active' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['username' => 'ali'],
            [
                'name' => 'Ali',
                'email' => null,
                'password' => Hash::make('ali123'),
                'role' => UserRole::Student,
                'birth_date' => '2016-03-15',
                'points_total' => 12,
                'is_active' => true,
            ]
        );

        $admin = User::query()->where('email', 'admin@edusphere.local')->first();

        if ($admin) {
            Announcement::query()->updateOrCreate(
                ['title' => 'Bienvenue à l\'école d\'été !'],
                [
                    'body' => 'Bonjour à tous ! Consulte tes matières chaque jour et n\'oublie pas de regarder les devoirs sur ton tableau de bord.',
                    'published_at' => now(),
                    'author_id' => $admin->id,
                ]
            );
        }
    }
}
