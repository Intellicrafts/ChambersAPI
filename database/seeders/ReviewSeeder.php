<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users and lawyers
        $userIds = \App\Models\User::pluck('id')->toArray();
        $lawyerIds = \App\Models\Lawyer::pluck('id')->toArray();
        
        // Make sure we have users and lawyers
        if (empty($userIds) || empty($lawyerIds)) {
            $this->command->info('No users or lawyers found. Please run UserSeeder and LawyerSeeder first.');
            return;
        }
        
        // Create 3 reviews for each lawyer (distributed among users)
        foreach ($lawyerIds as $lawyerId) {
            // Get 3 random users for this lawyer
            $randomUsers = array_rand(array_flip($userIds), min(3, count($userIds)));
            if (!is_array($randomUsers)) {
                $randomUsers = [$randomUsers];
            }
            
            foreach ($randomUsers as $userId) {
                \App\Models\Review::factory()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'user_id' => $userId,
                    'lawyer_id' => $lawyerId
                ]);
            }
        }
    }
}
