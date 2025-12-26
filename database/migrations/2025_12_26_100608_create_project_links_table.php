<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('title');
            $table->text('url');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_links');
    }
};
