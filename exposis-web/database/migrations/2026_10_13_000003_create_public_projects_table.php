<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('public_projects', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title', 150);
            $t->text('summary');
            $t->longText('body');                       // Markdown
            $t->string('pole', 40)->index();
            $t->string('status', 20);                   // Recherche | Prototype | MVP | Publié
            $t->string('repo_url')->nullable();
            $t->boolean('is_sample')->default(false);
            $t->timestamp('published_at')->nullable()->index(); // null = draft
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('public_projects'); }
};
