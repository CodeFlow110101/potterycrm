<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        User::create([
            'email' => env('ADMIN_EMAIL'),
            'password' => Hash::make('12345678'),
            'phoneno' => env('ADMIN_PHONE_NO'),
            'first_name' => 'admin',
            'last_name' => 'admin'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
