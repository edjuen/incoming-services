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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
	    $table->string('folio')->nullable();
	    $table->string('source')->nullable(); // AXA, manual, GLPI, etc.
	    $table->string('insurance_company')->nullable();
	    $table->string('service_type')->nullable();
	    $table->string('insured_name')->nullable();
	    $table->string('insured_phone')->nullable();
	    $table->text('origin_address')->nullable();
	    $table->string('origin_coordinates')->nullable();
	    $table->text('destination_address')->nullable();
	    $table->string('destination_coordinates')->nullable();
	    $table->string('vehicle')->nullable();
	    $table->string('status')->default('new');
	    $table->text('notes')->nullable();
	    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
