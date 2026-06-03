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
    	Schema::create('service_external_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('provider_name');
            $table->string('external_case_number')
                ->nullable();
            $table->string('external_service_id')
                ->nullable();
            $table->string('external_provider_service_id')
                ->nullable();
            $table->string('external_status')
                ->nullable();
            $table->json('payload')
                ->nullable();
            $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_external_references');
    }
};
