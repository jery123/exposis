<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('submitter_name')->nullable();
            $table->string('submitter_email')->nullable();
            $table->string('submitter_organization')->nullable();
            $table->string('submitter_phone', 40)->nullable();
        });
    }

    public function down(): void
    {
        if (DB::table('projects')->whereNull('user_id')->exists()) {
            throw new RuntimeException('Cannot roll back while visitor-submitted projects still exist.');
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['submitter_name', 'submitter_email', 'submitter_organization', 'submitter_phone']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
