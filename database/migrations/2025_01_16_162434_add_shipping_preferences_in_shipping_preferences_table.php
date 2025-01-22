<?php

use App\Models\ShippingPreference;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        ShippingPreference::create(['name' => 'pickup']);
        ShippingPreference::create(['name' => 'deliver']);
        ShippingPreference::create(['name' => 'in store']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
