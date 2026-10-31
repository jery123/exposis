<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('projects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('reference', 12)->unique();
            $t->string('title', 150);
            $t->string('request_type', 20);            // feasibility | prototype | research
            $t->text('problem');
            $t->text('description');
            $t->string('timeline', 150)->nullable();
            $t->string('budget', 150)->nullable();
            $t->boolean('confidential')->default(false);
            $t->string('status', 20)->default('submitted')->index();
            $t->string('nda_status', 20)->default('none'); // none | requested | signed
            $t->timestamps();
        });
        Schema::create('evaluations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('project_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('evaluator_id')->constrained('users');
            $t->unsignedTinyInteger('technical');  // 1-5, higher = more feasible
            $t->unsignedTinyInteger('resources');  // 1-5, higher = resources available
            $t->unsignedTinyInteger('impact');     // 1-5
            $t->unsignedTinyInteger('risk');       // 1-5, higher = riskier
            $t->string('recommendation', 20);      // go | conditional | no_go
            $t->text('summary');
            $t->boolean('shared')->default(false); // visible to the organization
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('evaluations'); Schema::dropIfExists('projects'); }
};
