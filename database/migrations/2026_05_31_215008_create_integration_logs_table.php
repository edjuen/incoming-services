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
    Schema::create('integration_logs', function (Blueprint $table) {
        $table->id();

        $table->foreignId('integration_provider_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->foreignId('service_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->string('direction')->nullable(); // incoming / outgoing
        $table->string('action')->nullable(); // login, fetch, accept, contact, finish, cancel
        $table->string('endpoint')->nullable();
        $table->integer('status_code')->nullable();
        $table->boolean('success')->default(false);

        $table->json('request_payload')->nullable();
        $table->json('response_payload')->nullable();

        $table->text('error_message')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_logs');
    }
};
