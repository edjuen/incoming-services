<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glpi_integrations', function (Blueprint $table) {
            $table->id();

            $table->string('name')->default('GLPI Principal');
            $table->string('base_url');

            // Se guardarán cifrados desde el modelo con cast encrypted
            $table->text('app_token')->nullable();
            $table->text('user_token')->nullable();

            $table->unsignedBigInteger('default_entity_id')->default(0);
            $table->boolean('verify_ssl')->default(true);
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glpi_integrations');
    }
};