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
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('container_id')->nullable()->unique(); // Docker container ID
            $table->string('name')->nullable();
            $table->enum('status', ['starting', 'running', 'stopped', 'error'])->default('starting');
            $table->string('port')->nullable(); // Port where the container is running
            $table->string('url')->nullable(); // Full URL to access the container
            $table->json('environment')->nullable(); // Environment variables
            $table->text('logs')->nullable(); // Container logs
            $table->timestamp('started_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->timestamps();
            
            $table->index(['project_id', 'status']);
            $table->index('container_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
