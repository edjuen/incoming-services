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
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
	    $table->foreignId('provider_id')
	        ->constrained()
	        ->cascadeOnDelete();
	    $table->string('operator_key')->nullable();
	    $table->string('first_name');
	    $table->string('last_name');
	    $table->string('second_last_name')->nullable();
	    $table->string('phone')->nullable();
	    $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
};
