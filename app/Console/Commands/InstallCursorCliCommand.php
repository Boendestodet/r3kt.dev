<?php

namespace App\Console\Commands;

use App\Services\CursorAIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class InstallCursorCliCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cursor:install 
                            {--check : Check if Cursor CLI is already installed}
                            {--force : Force reinstallation even if already installed}';

    /**
     * The console command description.
     */
    protected $description = 'Install Cursor CLI for AI-powered code generation';

    public function __construct(
        private CursorAIService $cursorService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🎯 Cursor CLI Installation Manager');
        $this->newLine();

        // Check if we're just checking installation status
        if ($this->option('check')) {
            return $this->checkInstallation();
        }

        // Check if already installed (unless force is used)
        if (! $this->option('force') && $this->cursorService->isCursorCliAvailable()) {
            $this->info('✅ Cursor CLI is already installed!');
            $this->newLine();

            if ($this->confirm('Would you like to check the installation details?')) {
                return $this->showInstallationDetails();
            }

            return Command::SUCCESS;
        }

        // Confirm installation
        $this->warn('This will install Cursor CLI on your system.');
        $this->info('Installation command: curl https://cursor.com/install -fsS | bash');
        $this->newLine();

        if (! $this->confirm('Do you want to proceed with the installation?')) {
            $this->info('Installation cancelled.');

            return Command::SUCCESS;
        }

        // Perform installation
        return $this->performInstallation();
    }

    /**
     * Check if Cursor CLI is installed
     */
    private function checkInstallation(): int
    {
        $this->info('🔍 Checking Cursor CLI installation...');
        $this->newLine();

        if ($this->cursorService->isCursorCliAvailable()) {
            $this->info('✅ Cursor CLI is installed and available');

            return $this->showInstallationDetails();
        } else {
            $this->error('❌ Cursor CLI is not installed or not available in PATH');
            $this->newLine();
            $this->info('To install Cursor CLI, run: php artisan cursor:install');

            return Command::FAILURE;
        }
    }

    /**
     * Show installation details
     */
    private function showInstallationDetails(): int
    {
        try {
            // Get cursor-agent version
            $versionProcess = Process::run(['cursor-agent', '--version']);

            if ($versionProcess->successful()) {
                $this->info('📋 Installation Details:');
                $this->line('  Version: '.trim($versionProcess->output()));
            }

            // Get cursor-agent path
            $whichProcess = Process::run(['which', 'cursor-agent']);

            if ($whichProcess->successful()) {
                $this->line('  Location: '.trim($whichProcess->output()));
            }

            $this->newLine();
            $this->info('🎯 Cursor CLI is ready for use!');
            $this->info('You can now use "Cursor CLI" as an AI model in your projects.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error getting installation details: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Perform the actual installation
     */
    private function performInstallation(): int
    {
        $this->info('📦 Installing Cursor CLI...');
        $this->newLine();

        try {
            // Show progress
            $this->withProgressBar(range(1, 3), function ($step) {
                switch ($step) {
                    case 1:
                        $this->task('Downloading installer...', function () {
                            sleep(1); // Simulate download time

                            return true;
                        });
                        break;
                    case 2:
                        $this->task('Running installation script...', function () {
                            $result = $this->cursorService->installCursorCli();

                            return $result['success'];
                        });
                        break;
                    case 3:
                        $this->task('Verifying installation...', function () {
                            sleep(1); // Give system time to update PATH

                            return $this->cursorService->isCursorCliAvailable();
                        });
                        break;
                }
            });

            $this->newLine();
            $this->newLine();

            // Final verification
            if ($this->cursorService->isCursorCliAvailable()) {
                $this->info('🎉 Cursor CLI installed successfully!');
                $this->newLine();

                return $this->showInstallationDetails();
            } else {
                $this->error('❌ Installation completed but Cursor CLI is not available');
                $this->warn('You may need to restart your terminal or update your PATH');

                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('Installation failed: '.$e->getMessage());
            $this->newLine();
            $this->info('Manual installation:');
            $this->line('  curl https://cursor.com/install -fsS | bash');

            return Command::FAILURE;
        }
    }
}
