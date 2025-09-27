<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FilePermissionService
{
    /**
     * Create a directory with proper permissions and ownership
     */
    public static function createDirectory(string $path, int $permissions = 0755): bool
    {
        try {
            // Create directory if it doesn't exist
            if (! is_dir($path)) {
                mkdir($path, $permissions, true);
            }

            // Set proper ownership to www-data
            self::setProperOwnership($path);

            Log::info('Directory created with proper permissions', [
                'path' => $path,
                'permissions' => decoct($permissions),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to create directory with proper permissions', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Set proper ownership for a file or directory
     */
    public static function setProperOwnership(string $path): bool
    {
        try {
            // Get the web server user and group
            $webUser = config('app.web_user', 'www-data');
            $webGroup = config('app.web_group', 'www-data');

            // Set ownership recursively
            chown($path, $webUser);
            chgrp($path, $webGroup);

            // If it's a directory, set ownership for all contents
            if (is_dir($path)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $item) {
                    if ($item->isFile() || $item->isDir()) {
                        chown($item->getPathname(), $webUser);
                        chgrp($item->getPathname(), $webGroup);
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::warning('Failed to set proper ownership', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Create a file with proper permissions and ownership
     */
    public static function createFile(string $path, string $content, int $permissions = 0644): bool
    {
        try {
            // Create directory if it doesn't exist
            $dir = dirname($path);
            if (! is_dir($dir)) {
                self::createDirectory($dir, 0755);
            }

            // Create the file
            file_put_contents($path, $content);

            // Set proper permissions and ownership
            chmod($path, $permissions);
            self::setProperOwnership($path);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to create file with proper permissions', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Ensure a project directory has proper permissions
     */
    public static function ensureProjectDirectoryPermissions(string $projectDir): bool
    {
        try {
            if (! is_dir($projectDir)) {
                return self::createDirectory($projectDir, 0755);
            }

            // Set proper ownership for existing directory
            self::setProperOwnership($projectDir);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to ensure project directory permissions', [
                'project_dir' => $projectDir,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
