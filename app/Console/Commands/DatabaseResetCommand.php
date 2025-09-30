<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'database:reset-increment 
                            {--force : Skip confirmation prompts}
                            {--dry-run : Show what would be reset without actually doing it}';

    /**
     * The console command description.
     */
    protected $description = 'Reset database auto-increment values to start from 1';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Database Auto-Increment Reset');
        $this->line('================================');

        if (!$this->option('force') && !$this->option('dry-run')) {
            if (!$this->confirm('This will reset all auto-increment values to start from 1. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->newLine();
        $this->info('📊 Current Auto-Increment Values:');
        $this->showCurrentValues();

        if (!$isDryRun) {
            $this->newLine();
            $this->info('🔄 Resetting Auto-Increment Values...');
            $this->resetIncrements();
        } else {
            $this->newLine();
            $this->info('🔍 Would reset auto-increment values for all tables');
        }

        $this->newLine();
        $this->info('✅ Database reset completed successfully!');
        
        return 0;
    }

    /**
     * Show current auto-increment values
     */
    private function showCurrentValues(): void
    {
        $tables = [
            'users',
            'projects', 
            'containers',
            'prompts',
            'chat_conversations',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $result = DB::select("SHOW TABLE STATUS LIKE '{$table}'");
                if (!empty($result)) {
                    $autoIncrement = $result[0]->Auto_increment ?? 'N/A';
                    $this->line("  {$table}: {$autoIncrement}");
                }
            }
        }
    }

    /**
     * Reset auto-increment values
     */
    private function resetIncrements(): void
    {
        $tables = [
            'users',
            'projects',
            'containers', 
            'prompts',
            'chat_conversations',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                try {
                    DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
                    $this->line("  ✅ Reset {$table} auto-increment to 1");
                } catch (\Exception $e) {
                    $this->error("  ❌ Failed to reset {$table}: " . $e->getMessage());
                }
            }
        }
    }
}