<?php

use App\Models\User;
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
        User::find(1)->update([
            'role_id' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
