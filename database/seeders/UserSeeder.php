<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $emails = [
            'superadmin@gmail.com',
            'admin@gmail.com',
            'user@gmail.com',
            'user1@gmail.com',
            'user2@gmail.com',
            'user3@gmail.com',
            'user4@gmail.com',
            'user5@gmail.com',
            'user6@gmail.com',
            'user7@gmail.com',
            'user8@gmail.com',
            'user9@gmail.com',
            'user10@gmail.com',
        ];

        foreach ($emails as $i => $email) {
            $user = User::create([
                'name' => ucfirst(explode('@', $email)[0]),
                'email' => $email,
                'password' => Hash::make('Password@123'),
            ]);

            UserProfile::create([
                'first_name' => ucfirst(explode('@', $email)[0]),
                'last_name' => ucfirst(explode('@', $email)[0]),
                'phone' => '123456789' . $i,
                'user_id' => $user->id,
            ]);
        }
    }
}
