<?php

use App\Models\ProductType;
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
        ProductType::create([
            'name' => 'online',
        ]);

        ProductType::create([
            'name' => 'in store',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
