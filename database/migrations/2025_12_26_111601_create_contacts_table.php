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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries');

            // Primary contact fields
            $table->string('primary_phone')->nullable();
            $table->string('primary_email')->unique();

            // JSON arrays for additional contacts
            $table->json('additional_phones')->nullable();
            $table->json('additional_emails')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('client_id');
            $table->index('country_id');
            $table->index('primary_email');
            $table->index('first_name');
            $table->index('last_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
