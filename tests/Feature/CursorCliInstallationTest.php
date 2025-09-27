<?php

use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class CursorCliInstallationTest extends TestCase
{
    public function test_cursor_install_command_checks_existing_installation(): void
    {
        // Mock Cursor CLI already installed
        Process::fake([
            'which cursor-agent' => Process::result(
                output: '/usr/local/bin/cursor-agent',
                exitCode: 0
            ),
        ]);

        $this->artisan('cursor:install')
            ->expectsQuestion('Would you like to check the installation details?', 'no')
            ->expectsOutput('✅ Cursor CLI is already installed!')
            ->assertExitCode(0);
    }

    public function test_cursor_install_command_with_check_option(): void
    {
        // Mock Cursor CLI installed
        Process::fake([
            'which cursor-agent' => Process::result(
                output: '/usr/local/bin/cursor-agent',
                exitCode: 0
            ),
            'cursor-agent --version' => Process::result(
                output: 'cursor-agent v1.0.0',
                exitCode: 0
            ),
        ]);

        $this->artisan('cursor:install --check')
            ->expectsOutput('🔍 Checking Cursor CLI installation...')
            ->expectsOutput('✅ Cursor CLI is installed and available')
            ->expectsOutput('📋 Installation Details:')
            ->expectsOutput('  Version: cursor-agent v1.0.0')
            ->assertExitCode(0);
    }

    public function test_cursor_install_command_check_fails_when_not_installed(): void
    {
        // Mock Cursor CLI not installed
        Process::fake([
            'which cursor-agent' => Process::result(
                output: '',
                exitCode: 1
            ),
        ]);

        $this->artisan('cursor:install --check')
            ->expectsOutput('🔍 Checking Cursor CLI installation...')
            ->expectsOutput('❌ Cursor CLI is not installed or not available in PATH')
            ->expectsOutput('To install Cursor CLI, run: php artisan cursor:install')
            ->assertExitCode(1);
    }

    public function test_cursor_install_command_cancels_when_user_declines(): void
    {
        // Mock Cursor CLI not installed
        Process::fake([
            'which cursor-agent' => Process::result(
                output: '',
                exitCode: 1
            ),
        ]);

        $this->artisan('cursor:install')
            ->expectsQuestion('Do you want to proceed with the installation?', 'no')
            ->expectsOutput('Installation cancelled.')
            ->assertExitCode(0);
    }

    public function test_cursor_install_command_performs_installation(): void
    {
        // Mock installation process
        Process::fake([
            'which cursor-agent' => Process::sequence()
                ->push(Process::result('', 1)) // Not installed initially
                ->push(Process::result('/usr/local/bin/cursor-agent', 0)), // Installed after
            'bash -c "curl https://cursor.com/install -fsS | bash"' => Process::result(
                output: 'Installation successful',
                exitCode: 0
            ),
            'cursor-agent --version' => Process::result(
                output: 'cursor-agent v1.0.0',
                exitCode: 0
            ),
        ]);

        $this->artisan('cursor:install')
            ->expectsQuestion('Do you want to proceed with the installation?', 'yes')
            ->expectsOutput('📦 Installing Cursor CLI...')
            ->expectsOutput('🎉 Cursor CLI installed successfully!')
            ->expectsOutput('📋 Installation Details:')
            ->expectsOutput('  Version: cursor-agent v1.0.0')
            ->assertExitCode(0);
    }

    public function test_cursor_install_command_handles_installation_failure(): void
    {
        // Mock failed installation
        Process::fake([
            'which cursor-agent' => Process::sequence()
                ->push(Process::result('', 1)) // Not installed initially
                ->push(Process::result('', 1)), // Still not installed after
            'bash -c "curl https://cursor.com/install -fsS | bash"' => Process::result(
                output: '',
                errorOutput: 'Installation failed',
                exitCode: 1
            ),
        ]);

        $this->artisan('cursor:install')
            ->expectsQuestion('Do you want to proceed with the installation?', 'yes')
            ->expectsOutput('📦 Installing Cursor CLI...')
            ->expectsOutput('❌ Installation completed but Cursor CLI is not available')
            ->expectsOutput('You may need to restart your terminal or update your PATH')
            ->assertExitCode(1);
    }

    public function test_cursor_install_command_with_force_option(): void
    {
        // Mock installation with force option
        Process::fake([
            'which cursor-agent' => Process::result(
                output: '/usr/local/bin/cursor-agent',
                exitCode: 0
            ),
            'bash -c "curl https://cursor.com/install -fsS | bash"' => Process::result(
                output: 'Reinstallation successful',
                exitCode: 0
            ),
            'cursor-agent --version' => Process::result(
                output: 'cursor-agent v1.1.0',
                exitCode: 0
            ),
        ]);

        $this->artisan('cursor:install --force')
            ->expectsQuestion('Do you want to proceed with the installation?', 'yes')
            ->expectsOutput('📦 Installing Cursor CLI...')
            ->expectsOutput('🎉 Cursor CLI installed successfully!')
            ->expectsOutput('  Version: cursor-agent v1.1.0')
            ->assertExitCode(0);
    }

    public function test_cursor_install_shows_installation_details_with_path(): void
    {
        // Mock complete installation details
        Process::fake([
            'which cursor-agent' => Process::result(
                output: '/usr/local/bin/cursor-agent',
                exitCode: 0
            ),
            'cursor-agent --version' => Process::result(
                output: 'cursor-agent v1.0.0',
                exitCode: 0
            ),
        ]);

        $this->artisan('cursor:install --check')
            ->expectsOutput('📋 Installation Details:')
            ->expectsOutput('  Version: cursor-agent v1.0.0')
            ->expectsOutput('  Location: /usr/local/bin/cursor-agent')
            ->expectsOutput('🎯 Cursor CLI is ready for use!')
            ->expectsOutput('You can now use "Cursor CLI" as an AI model in your projects.')
            ->assertExitCode(0);
    }

    public function test_cursor_install_handles_version_command_failure(): void
    {
        // Mock installation check with version command failure
        Process::fake([
            'which cursor-agent' => Process::result(
                output: '/usr/local/bin/cursor-agent',
                exitCode: 0
            ),
            'cursor-agent --version' => Process::result(
                output: '',
                errorOutput: 'Version command failed',
                exitCode: 1
            ),
        ]);

        $this->artisan('cursor:install --check')
            ->expectsOutput('✅ Cursor CLI is installed and available')
            ->expectsOutput('🎯 Cursor CLI is ready for use!')
            ->assertExitCode(0);
    }
}