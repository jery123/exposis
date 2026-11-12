<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_project_id')->constrained()->cascadeOnDelete();
            $table->string('path')->unique();
            $table->string('original_name', 200);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_project_images');
    }
};
