<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {

            $table->foreignId('integration_provider_id')
                ->nullable()
                ->after('insurance_company_id')
                ->constrained()
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {

            $table->dropConstrainedForeignId('integration_provider_id');

        });
    }
};