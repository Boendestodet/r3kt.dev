<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to projects table for better performance
        Schema::table('projects', function (Blueprint $table) {
            if (!$this->indexExists('projects', 'projects_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->indexExists('projects', 'projects_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('projects', 'projects_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->indexExists('projects', 'projects_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
            if (!$this->indexExists('projects', 'projects_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at']);
            }
        });

        // Add indexes to containers table
        Schema::table('containers', function (Blueprint $table) {
            if (!$this->indexExists('containers', 'containers_project_id_index')) {
                $table->index('project_id');
            }
            if (!$this->indexExists('containers', 'containers_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('containers', 'containers_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->indexExists('containers', 'containers_project_id_status_index')) {
                $table->index(['project_id', 'status']);
            }
        });

        // Add indexes to prompts table
        Schema::table('prompts', function (Blueprint $table) {
            if (!$this->indexExists('prompts', 'prompts_project_id_index')) {
                $table->index('project_id');
            }
            if (!$this->indexExists('prompts', 'prompts_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->indexExists('prompts', 'prompts_project_id_created_at_index')) {
                $table->index(['project_id', 'created_at']);
            }
        });

        // Add indexes to comments table
        Schema::table('comments', function (Blueprint $table) {
            if (!$this->indexExists('comments', 'comments_project_id_index')) {
                $table->index('project_id');
            }
            if (!$this->indexExists('comments', 'comments_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->indexExists('comments', 'comments_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->indexExists('comments', 'comments_project_id_created_at_index')) {
                $table->index(['project_id', 'created_at']);
            }
        });

        // Add indexes to chat_conversations table
        Schema::table('chat_conversations', function (Blueprint $table) {
            if (!$this->indexExists('chat_conversations', 'chat_conversations_project_id_index')) {
                $table->index('project_id');
            }
            if (!$this->indexExists('chat_conversations', 'chat_conversations_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->indexExists('chat_conversations', 'chat_conversations_project_id_created_at_index')) {
                $table->index(['project_id', 'created_at']);
            }
            if (!$this->indexExists('chat_conversations', 'chat_conversations_chat_id_index')) {
                $table->index('chat_id');
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        return collect($indexes)->contains('Key_name', $index);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        // Remove indexes from containers table
        Schema::table('containers', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['project_id', 'status']);
        });

        // Remove indexes from prompts table
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['project_id', 'created_at']);
        });

        // Remove indexes from comments table
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['project_id', 'created_at']);
        });

        // Remove indexes from chat_conversations table
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['project_id', 'created_at']);
            $table->dropIndex(['chat_id']);
        });
    }
};