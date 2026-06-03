<?php

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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
	    $table->foreignId('provider_id')
            	->constrained()
            	->cascadeOnDelete();

       $table->string('unit_key')
            ->nullable();

        $table->string('brand')
            ->nullable();

        $table->string('model')
            ->nullable();

        $table->string('year')
            ->nullable();

        $table->string('color')
            ->nullable();

        $table->string('plates')
            ->nullable();

        $table->string('vin')
            ->nullable();

        $table->string('unit_type')
            ->nullable();

        $table->boolean('is_active')
            ->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
