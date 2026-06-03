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
        Schema::create('integration_providers', function (Blueprint $table) {
            $table->id();
	    $table->foreignId('insurance_company_id')
	    	->nullable()
	    	->constrained()
	    	->nullOnDelete();
	    $table->string('name'); // AXA, GLPI, etc.
	    $table->string('code')->unique(); // AXA, GLPI
	    $table->string('base_url')->nullable();
	    $table->string('public_key')->nullable();
	    $table->text('secret_key')->nullable();
	    $table->string('username')->nullable();
	    $table->text('password')->nullable();
	    $table->json('settings')->nullable();
	    $table->text('description')->nullable();
	    $table->boolean('is_active')->default(true);
	    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_providers');
    }
};
