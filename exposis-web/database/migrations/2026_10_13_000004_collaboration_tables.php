<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_messages', function (Blueprint $t) {
            $t->id(); $t->foreignId('project_id')->constrained()->cascadeOnDelete(); $t->foreignId('user_id')->constrained();
            $t->text('body'); $t->boolean('is_internal')->default(false); $t->timestamps();
        });
        Schema::create('project_files', function (Blueprint $t) {
            $t->id(); $t->foreignId('project_id')->constrained()->cascadeOnDelete(); $t->foreignId('user_id')->constrained();
            $t->string('original_name'); $t->string('path'); $t->unsignedInteger('size'); $t->timestamps();
        });
        Schema::create('project_events', function (Blueprint $t) {
            $t->id(); $t->foreignId('project_id')->constrained()->cascadeOnDelete(); $t->foreignId('user_id')->nullable()->constrained();
            $t->string('type', 20); $t->string('from', 20)->nullable(); $t->string('to', 20)->nullable(); $t->timestamps();
        });
    }
    public function down(): void { foreach (['project_events', 'project_files', 'project_messages'] as $x) Schema::dropIfExists($x); }
};
