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
    Schema::table('integration_providers', function (Blueprint $table) {
        $table->text('access_token')->nullable()->after('secret_key');
        $table->timestamp('token_expires_at')->nullable()->after('access_token');
        $table->timestamp('last_login_at')->nullable()->after('token_expires_at');
    });
}

public function down(): void
{
    Schema::table('integration_providers', function (Blueprint $table) {
        $table->dropColumn([
            'access_token',
            'token_expires_at',
            'last_login_at',
        ]);
    });
}
};
