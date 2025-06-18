<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvailabilitySlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing lawyers
        $lawyerIds = \App\Models\Lawyer::pluck('id')->toArray();
        
        // Make sure we have lawyers
        if (empty($lawyerIds)) {
            $this->command->info('No lawyers found. Please run LawyerSeeder first.');
            return;
        }
        
        // Create 5 availability slots for each lawyer
        foreach ($lawyerIds as $lawyerId) {
            \App\Models\AvailabilitySlot::factory()->count(5)->make()->each(function ($slot) use ($lawyerId) {
                $slot->lawyer_id = $lawyerId;
                $slot->save();
            });
        }
    }
}
