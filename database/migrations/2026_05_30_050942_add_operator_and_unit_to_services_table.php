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
        Schema::table('services', function (Blueprint $table) {
             $table->foreignId('operator_id')
            ->nullable()
            ->after('provider_id')
            ->constrained()
            ->nullOnDelete();

        $table->foreignId('unit_id')
            ->nullable()
            ->after('operator_id')
            ->constrained()
            ->nullOnDelete();

	$table->integer('estimated_arrival_minutes')
		->nullable()
		->after('unit_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            //
        });
    }
};
