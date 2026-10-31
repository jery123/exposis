<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('public_projects', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('team_members')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('public_projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('team_members');
        });
    }
};
