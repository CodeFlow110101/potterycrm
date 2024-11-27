<?php

use App\Models\PostalCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('postcode');
            $table->text('place');
            $table->text('state');
            $table->text('state_code');
            $table->timestamps();
        });

        $jsonData = File::get(public_path('postalCodes/au_postcodes.json'));
        $currentTimestamp = now();
        $data = [];
        foreach (json_decode($jsonData) as $code) {
            $data[] = ['postcode' => $code->postcode, 'place' => $code->place_name, 'state' => $code->state_name, 'state_code' => $code->state_code, 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp];
        }

        $dataChunks = array_chunk($data, 1000);
        foreach ($dataChunks as $data) {
            PostalCode::insert($data);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postal_codes');
    }
};
