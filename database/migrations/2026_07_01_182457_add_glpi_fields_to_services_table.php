<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('glpi_ticket_id')->nullable()->index();
            $table->timestamp('glpi_migrated_at')->nullable();

            $table->foreignId('glpi_migrated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('glpi_migration_error')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['glpi_migrated_by']);

            $table->dropColumn([
                'glpi_ticket_id',
                'glpi_migrated_at',
                'glpi_migrated_by',
                'glpi_migration_error',
            ]);
        });
    }
};
