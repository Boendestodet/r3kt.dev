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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('chat_id')->unique();
            $table->json('messages')->nullable(); // Store parsed messages
            $table->text('raw_conversation')->nullable(); // Store raw Cursor CLI output
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
            
            $table->index(['project_id', 'chat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};