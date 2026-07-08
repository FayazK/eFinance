<?php

declare(strict_types=1);

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
            // A contact email is unique per client, not globally, so different
            // clients may each have a contact with the same email address.
            $table->dropUnique(['primary_email']);
            $table->unique(['client_id', 'primary_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropUnique(['client_id', 'primary_email']);
            $table->unique(['primary_email']);
        });
    }
};
