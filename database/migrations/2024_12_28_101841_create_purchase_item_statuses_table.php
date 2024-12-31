<?php

use App\Models\PurchaseItemStatus;
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
        Schema::create('purchase_item_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        PurchaseItemStatus::create(['name' => 'ready for firing']);
        PurchaseItemStatus::create(['name' => 'firing in progress']);
        PurchaseItemStatus::create(['name' => 'firing process finished']);
        PurchaseItemStatus::create(['name' => 'ready for pickup']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_item_statuses');
    }
};
