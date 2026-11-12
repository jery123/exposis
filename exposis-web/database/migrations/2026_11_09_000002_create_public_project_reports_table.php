<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_project_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('public_project_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('path')->unique();
            $table->string('original_name', 200);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_project_reports');
    }
};
