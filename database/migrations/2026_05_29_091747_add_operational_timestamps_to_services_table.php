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
        	$table->timestamp('assigned_at')->nullable();
        	$table->timestamp('accepted_at')->nullable();
        	$table->timestamp('on_route_at')->nullable();
        	$table->timestamp('on_scene_at')->nullable();
        	$table->timestamp('completed_at')->nullable();
        	$table->timestamp('cancelled_at')->nullable();
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
