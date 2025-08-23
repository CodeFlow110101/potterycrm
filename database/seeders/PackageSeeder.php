<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\TimeSlot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Package::find(1)) {
            $package = Package::create([
                'name' => 'Regular Painting Session',
                'description' => 'None',
            ]);

            $package->packageTimeSlots()->createMany(TimeSlot::pluck('id')->map(fn($id) => ['time_slot_id' => $id]));
        }
    }
}
