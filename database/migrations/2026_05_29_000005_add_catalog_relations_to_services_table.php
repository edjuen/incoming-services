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
        $table->foreignId('insurance_company_id')
            ->nullable()
            ->after('id')
            ->constrained('insurance_companies')
            ->nullOnDelete();

        $table->foreignId('service_type_id')
            ->nullable()
            ->after('insurance_company_id')
            ->constrained('service_types')
            ->nullOnDelete();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
        $table->dropConstrainedForeignId('insurance_company_id');
        $table->dropConstrainedForeignId('service_type_id');
    });
    }
};
