<?php

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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->enum('status', ['draft', 'building', 'ready', 'error'])->default('draft');
            $table->json('settings')->nullable(); // Store project settings like theme, framework, etc.
            $table->text('generated_code')->nullable(); // Store the generated HTML/CSS/JS
            $table->string('preview_url')->nullable(); // URL where the project is hosted
            $table->boolean('is_public')->default(false);
            $table->timestamp('last_built_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
