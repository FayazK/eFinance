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
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('state_id')->nullable()->after('country_id')->constrained('states');
            $table->foreignId('city_id')->nullable()->after('state_id')->constrained('cities');
            $table->dropColumn(['state', 'city']);
            $table->index('state_id');
            $table->index('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('state_id');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
        });
    }
};
