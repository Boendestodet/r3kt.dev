<?php

namespace App\Console\Commands;

use App\Models\Container;
use App\Models\Project;
use App\Models\Prompt;
use App\Models\User;
use App\Services\DockerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class ProjectCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'projects:cleanup 
                            {--all : Clean up all projects}
                            {--project= : Clean up specific project by ID}
                            {--force : Skip confirmation prompts}
                            {--docker-only : Only clean Docker resources}
                            {--files-only : Only clean project files}
                            {--database-only : Only clean database records}
                            {--reset-database : Reset entire database and start fresh from port 8000}
                            {--dry-run : Show what would be cleaned without actually doing it}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up projects by removing files, Docker containers, and database records';

    public function __construct(
        private DockerService $dockerService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧹 Project Cleanup Command');
        $this->newLine();

        // Validate options
        if (! $this->validateOptions()) {
            return Command::FAILURE;
        }

        $isDryRun = $this->option('dry-run');
        $isForce = $this->option('force');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual changes will be made');
            $this->newLine();
        }

        // Check if this is a database reset
        $resetDatabase = $this->option('reset-database');

        if ($resetDatabase) {
            return $this->handleDatabaseReset($isDryRun, $isForce);
        }

        // Determine what to clean
        $cleanAll = $this->option('all');
        $projectId = $this->option('project');
        $dockerOnly = $this->option('docker-only');
        $filesOnly = $this->option('files-only');
        $databaseOnly = $this->option('database-only');

        // Get projects to clean
        $projects = $this->getProjectsToClean($cleanAll, $projectId);

        if ($projects->isEmpty()) {
            $this->warn('No projects found to clean up.');

            return Command::SUCCESS;
        }

        // Show what will be cleaned
        $this->showCleanupPlan($projects, $dockerOnly, $filesOnly, $databaseOnly, $cleanAll, $isDryRun);

        // Confirm before proceeding (skip confirmation for --all unless --dry-run)
        if (! $isDryRun && ! $isForce && ! $cleanAll) {
            if (! $this->confirm('Are you sure you want to proceed with the cleanup?')) {
                $this->info('Cleanup cancelled.');

                return Command::SUCCESS;
            }
        }

        // Perform cleanup
        $results = $this->performCleanup($projects, $dockerOnly, $filesOnly, $databaseOnly, $isDryRun);

        // Reset auto-increment counters if using --all
        if ($cleanAll && ! $isDryRun) {
            $this->resetAutoIncrementCounters();
        }

        // Show results
        $this->showResults($results, $isDryRun);

        return Command::SUCCESS;
    }

    /**
     * Validate command options
     */
    private function validateOptions(): bool
    {
        $all = $this->option('all');
        $project = $this->option('project');
        $dockerOnly = $this->option('docker-only');
        $filesOnly = $this->option('files-only');
        $databaseOnly = $this->option('database-only');
        $resetDatabase = $this->option('reset-database');

        // Check for conflicting options
        $exclusiveOptions = array_filter([$dockerOnly, $filesOnly, $databaseOnly, $resetDatabase]);
        if (count($exclusiveOptions) > 1) {
            $this->error('You can only use one of: --docker-only, --files-only, --database-only, --reset-database');

            return false;
        }

        // Check for required options
        if (! $all && ! $project && ! $resetDatabase) {
            $this->error('You must specify either --all, --project=ID, or --reset-database');

            return false;
        }

        // Validate project ID if provided
        if ($project && ! is_numeric($project)) {
            $this->error('Project ID must be a number');

            return false;
        }

        return true;
    }

    /**
     * Handle database reset
     */
    private function handleDatabaseReset(bool $isDryRun, bool $isForce): int
    {
        $this->info('🔄 Database Reset Mode');
        $this->newLine();

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual changes will be made');
            $this->newLine();
        }

        // Show what will be reset
        $this->showDatabaseResetPlan($isDryRun);

        // Confirm before proceeding
        if (! $isDryRun && ! $isForce) {
            $this->warn('⚠️  This will completely reset the database and remove ALL data!');
            if (! $this->confirm('Are you absolutely sure you want to proceed?')) {
                $this->info('Database reset cancelled.');

                return Command::SUCCESS;
            }
        }

        // Perform database reset
        $results = $this->performDatabaseReset($isDryRun);

        // Show results
        $this->showDatabaseResetResults($results, $isDryRun);

        return Command::SUCCESS;
    }

    /**
     * Show database reset plan
     */
    private function showDatabaseResetPlan(bool $isDryRun): void
    {
        $this->info('📋 Database Reset Plan:');
        $this->newLine();

        // Get current counts
        $projectCount = Project::count();
        $containerCount = Container::count();
        $promptCount = Prompt::count();
        $userCount = User::count();

        $this->table(
            ['Table', 'Current Records', 'Action'],
            [
                ['Projects', $projectCount, 'Delete all'],
                ['Containers', $containerCount, 'Delete all'],
                ['Prompts', $promptCount, 'Delete all'],
                ['Users', $userCount, 'Keep (preserve user accounts)'],
            ]
        );

        $this->newLine();
        $this->info('Actions to be performed:');
        $this->line('🐳 Stop and remove ALL Docker containers and images');
        $this->line('📁 Remove ALL project files from storage');
        $this->line('🗄️  Reset database tables (except users)');
        $this->line('🔄 Reset auto-increment counters to start from 1');
        $this->line('🚀 Next project will start on port 8000');

        $this->newLine();
    }

    /**
     * Perform database reset
     */
    private function performDatabaseReset(bool $isDryRun): array
    {
        $results = [
            'containers_removed' => 0,
            'images_removed' => 0,
            'files_removed' => 0,
            'database_records_removed' => 0,
            'errors' => [],
        ];

        try {
            // 1. Stop and remove ALL Docker containers and images
            $this->info('🐳 Cleaning ALL Docker resources...');
            $dockerResults = $this->cleanupAllDockerResources($isDryRun);
            $results['containers_removed'] = $dockerResults['containers_removed'];
            $results['images_removed'] = $dockerResults['images_removed'];
            $results['errors'] = array_merge($results['errors'], $dockerResults['errors']);

            // 2. Remove ALL project files
            $this->info('📁 Cleaning ALL project files...');
            $fileResults = $this->cleanupAllProjectFiles($isDryRun);
            $results['files_removed'] = $fileResults['files_removed'];
            $results['errors'] = array_merge($results['errors'], $fileResults['errors']);

            // 3. Reset database
            $this->info('🗄️  Resetting database...');
            $dbResults = $this->resetDatabaseTables($isDryRun);
            $results['database_records_removed'] = $dbResults['records_removed'];
            $results['errors'] = array_merge($results['errors'], $dbResults['errors']);

        } catch (\Exception $e) {
            $error = 'Database reset error: '.$e->getMessage();
            $results['errors'][] = $error;
            $this->error($error);
        }

        return $results;
    }

    /**
     * Clean up ALL Docker resources
     */
    private function cleanupAllDockerResources(bool $isDryRun): array
    {
        $results = [
            'containers_removed' => 0,
            'images_removed' => 0,
            'errors' => [],
        ];

        try {
            if (! $isDryRun) {
                // First, get list of running lovable containers
                $runningContainers = Process::run('docker ps -q --filter "name=lovable-"');
                $runningCount = 0;
                if ($runningContainers->successful() && trim($runningContainers->output())) {
                    $runningCount = count(array_filter(explode("\n", trim($runningContainers->output()))));
                    $this->line("  📋 Found {$runningCount} running lovable containers");
                }

                // Stop all running lovable containers with timeout
                if ($runningCount > 0) {
                    $this->line('  🛑 Stopping all running lovable containers...');
                    $stopResult = Process::timeout(30)->run('docker ps -q --filter "name=lovable-" | xargs -r docker stop');
                    if ($stopResult->successful()) {
                        $this->line("  ✅ Stopped {$runningCount} lovable containers");
                    } else {
                        $this->warn('  ⚠️  Some containers may not have stopped gracefully');
                        // Force kill if stop didn't work
                        $killResult = Process::run('docker ps -q --filter "name=lovable-" | xargs -r docker kill');
                        if ($killResult->successful()) {
                            $this->line('  ✅ Force killed remaining containers');
                        }
                    }
                } else {
                    $this->line('  ℹ️  No running lovable containers found');
                }

                // Wait a moment for containers to fully stop
                if ($runningCount > 0) {
                    $this->line('  ⏳ Waiting for containers to fully stop...');
                    sleep(2);
                }

                // Remove all lovable containers (both stopped and exited)
                $removeResult = Process::run('docker ps -aq --filter "name=lovable-" | xargs -r docker rm -f');
                if ($removeResult->successful()) {
                    $containerOutput = trim($removeResult->output());
                    $containerCount = $containerOutput ? count(array_filter(explode("\n", $containerOutput))) : 0;
                    if ($containerCount > 0) {
                        $this->line("  ✅ Removed {$containerCount} lovable containers");
                    } else {
                        $this->line('  ℹ️  No containers to remove');
                    }
                    $results['containers_removed'] = $containerCount;
                }

                // Remove all lovable images (only if no containers are using them)
                $imageResult = Process::run('docker images -q --filter "reference=lovable-project-*" | xargs -r docker rmi -f');
                if ($imageResult->successful()) {
                    $imageOutput = trim($imageResult->output());
                    $imageCount = $imageOutput ? count(array_filter(explode("\n", $imageOutput))) : 0;
                    if ($imageCount > 0) {
                        $this->line("  ✅ Removed {$imageCount} lovable images");
                    } else {
                        $this->line('  ℹ️  No images to remove');
                    }
                    $results['images_removed'] = $imageCount;
                }
            } else {
                // Dry run - check what would be affected
                $runningContainers = Process::run('docker ps -q --filter "name=lovable-"');
                $allContainers = Process::run('docker ps -aq --filter "name=lovable-"');
                $images = Process::run('docker images -q --filter "reference=lovable-project-*"');

                $runningCount = $runningContainers->successful() && trim($runningContainers->output()) ?
                    count(array_filter(explode("\n", trim($runningContainers->output())))) : 0;
                $totalContainers = $allContainers->successful() && trim($allContainers->output()) ?
                    count(array_filter(explode("\n", trim($allContainers->output())))) : 0;
                $imageCount = $images->successful() && trim($images->output()) ?
                    count(array_filter(explode("\n", trim($images->output())))) : 0;

                $this->line("  🔍 Would stop {$runningCount} running containers");
                $this->line("  🔍 Would remove {$totalContainers} total containers");
                $this->line("  🔍 Would remove {$imageCount} images");

                $results['containers_removed'] = $totalContainers;
                $results['images_removed'] = $imageCount;
            }

        } catch (\Exception $e) {
            $error = 'Docker cleanup error: '.$e->getMessage();
            $results['errors'][] = $error;
            $this->error("  ❌ {$error}");
        }

        return $results;
    }

    /**
     * Clean up ALL project files
     */
    private function cleanupAllProjectFiles(bool $isDryRun): array
    {
        $results = [
            'files_removed' => 0,
            'errors' => [],
        ];

        try {
            $projectsDir = storage_path('app/projects');

            if (is_dir($projectsDir)) {
                if (! $isDryRun) {
                    // Count files before deletion
                    $fileCount = count(File::allFiles($projectsDir));

                    // Remove all project directories
                    if (File::deleteDirectory($projectsDir)) {
                        $this->line("  ✅ Removed all project files ({$fileCount} files)");
                        $results['files_removed'] = $fileCount;
                    } else {
                        $results['errors'][] = "Failed to remove projects directory: {$projectsDir}";
                    }
                } else {
                    $fileCount = is_dir($projectsDir) ? count(File::allFiles($projectsDir)) : 0;
                    $this->line("  🔍 Would remove all project files ({$fileCount} files)");
                    $results['files_removed'] = $fileCount;
                }
            } else {
                $this->line('  ℹ️  Projects directory does not exist');
            }

        } catch (\Exception $e) {
            $error = 'File cleanup error: '.$e->getMessage();
            $results['errors'][] = $error;
            $this->error("  ❌ {$error}");
        }

        return $results;
    }

    /**
     * Reset database tables
     */
    private function resetDatabaseTables(bool $isDryRun): array
    {
        $results = [
            'records_removed' => 0,
            'errors' => [],
        ];

        try {
            if (! $isDryRun) {
                // Get counts before deletion
                $projectCount = Project::count();
                $containerCount = Container::count();
                $promptCount = Prompt::count();
                $commentCount = DB::table('comments')->count();
                $totalRecords = $projectCount + $containerCount + $promptCount + $commentCount;

                // Disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                // Delete all records (order matters due to foreign keys)
                DB::table('comments')->delete();
                Container::truncate();
                Prompt::truncate();
                Project::truncate();

                // Reset auto-increment counters
                DB::statement('ALTER TABLE projects AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE containers AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE prompts AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE comments AUTO_INCREMENT = 1');

                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                $this->line('  ✅ Reset database tables and auto-increment counters');
                $results['records_removed'] = $totalRecords;
            } else {
                $projectCount = Project::count();
                $containerCount = Container::count();
                $promptCount = Prompt::count();
                $commentCount = DB::table('comments')->count();
                $totalRecords = $projectCount + $containerCount + $promptCount + $commentCount;

                $this->line("  🔍 Would reset database tables and remove {$totalRecords} records");
                $results['records_removed'] = $totalRecords;
            }

        } catch (\Exception $e) {
            $error = 'Database reset error: '.$e->getMessage();
            $results['errors'][] = $error;
            $this->error("  ❌ {$error}");
        }

        return $results;
    }

    /**
     * Show database reset results
     */
    private function showDatabaseResetResults(array $results, bool $isDryRun): void
    {
        $this->newLine();
        $this->info('📊 Database Reset Results:');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Containers Removed', $results['containers_removed']],
                ['Images Removed', $results['images_removed']],
                ['Files Removed', $results['files_removed']],
                ['Database Records Removed', $results['database_records_removed']],
                ['Errors', count($results['errors'])],
            ]
        );

        if (! empty($results['errors'])) {
            $this->newLine();
            $this->error('❌ Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("  • {$error}");
            }
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn('🔍 This was a dry run - no actual changes were made.');
            $this->info('Run the command without --dry-run to perform the actual reset.');
        } else {
            $this->newLine();
            $this->info('✅ Database reset completed successfully!');
            $this->info('🚀 Next project will start on port 8000');
        }
    }

    /**
     * Reset auto-increment counters for all tables
     */
    private function resetAutoIncrementCounters(): void
    {
        try {
            $this->info('🔄 Resetting auto-increment counters...');

            // Reset auto-increment counters to start from 1
            DB::statement('ALTER TABLE projects AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE containers AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE prompts AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE comments AUTO_INCREMENT = 1');

            $this->line('  ✅ Reset auto-increment counters - next project will start at ID 1');

        } catch (\Exception $e) {
            $this->error('  ❌ Failed to reset auto-increment counters: '.$e->getMessage());
        }
    }

    /**
     * Get projects to clean up
     */
    private function getProjectsToClean(bool $cleanAll, ?string $projectId)
    {
        if ($cleanAll) {
            return Project::with(['containers', 'prompts'])->get();
        }

        if ($projectId) {
            $project = Project::with(['containers', 'prompts'])->find($projectId);
            if (! $project) {
                $this->error("Project with ID {$projectId} not found.");

                return collect();
            }

            return collect([$project]);
        }

        return collect();
    }

    /**
     * Show what will be cleaned
     */
    private function showCleanupPlan($projects, bool $dockerOnly, bool $filesOnly, bool $databaseOnly, bool $cleanAll, bool $isDryRun): void
    {
        $this->info('📋 Cleanup Plan:');
        $this->newLine();

        $this->table(
            ['Project ID', 'Name', 'Status', 'Containers', 'Prompts', 'Files'],
            $projects->map(function ($project) {
                $projectDir = storage_path("app/projects/{$project->id}");
                $hasFiles = is_dir($projectDir) && count(File::allFiles($projectDir)) > 0;

                return [
                    $project->id,
                    $project->name,
                    $project->status,
                    $project->containers->count(),
                    $project->prompts->count(),
                    $hasFiles ? 'Yes' : 'No',
                ];
            })
        );

        $this->newLine();
        $this->info('Actions to be performed:');

        if ($dockerOnly) {
            $this->line('🐳 Docker containers and images cleanup');
        } elseif ($filesOnly) {
            $this->line('📁 Project files cleanup');
        } elseif ($databaseOnly) {
            $this->line('🗄️  Database records cleanup');
        } else {
            $this->line('🐳 Docker containers and images cleanup');
            $this->line('📁 Project files cleanup');
            $this->line('🗄️  Database records cleanup');
        }

        // Show warning for --all
        if ($cleanAll && ! $isDryRun) {
            $this->newLine();
            $this->warn('⚠️  Using --all will clean up ALL projects without confirmation!');
            $this->info('🔄 Auto-increment counters will be reset - next project starts at ID 1');
        }

        $this->newLine();
    }

    /**
     * Perform the cleanup
     */
    private function performCleanup($projects, bool $dockerOnly, bool $filesOnly, bool $databaseOnly, bool $isDryRun): array
    {
        $results = [
            'projects_processed' => 0,
            'containers_removed' => 0,
            'images_removed' => 0,
            'files_removed' => 0,
            'database_records_removed' => 0,
            'errors' => [],
        ];

        foreach ($projects as $project) {
            $this->info("Processing project: {$project->name} (ID: {$project->id})");

            try {
                // Clean Docker resources
                if (! $filesOnly && ! $databaseOnly) {
                    $dockerResults = $this->cleanupDockerResources($project, $isDryRun);
                    $results['containers_removed'] += $dockerResults['containers_removed'];
                    $results['images_removed'] += $dockerResults['images_removed'];
                    $results['errors'] = array_merge($results['errors'], $dockerResults['errors']);
                }

                // Clean project files
                if (! $dockerOnly && ! $databaseOnly) {
                    $fileResults = $this->cleanupProjectFiles($project, $isDryRun);
                    $results['files_removed'] += $fileResults['files_removed'];
                    $results['errors'] = array_merge($results['errors'], $fileResults['errors']);
                }

                // Clean database records
                if (! $dockerOnly && ! $filesOnly) {
                    $dbResults = $this->cleanupDatabaseRecords($project, $isDryRun);
                    $results['database_records_removed'] += $dbResults['records_removed'];
                    $results['errors'] = array_merge($results['errors'], $dbResults['errors']);
                }

                $results['projects_processed']++;

            } catch (\Exception $e) {
                $error = "Error processing project {$project->id}: ".$e->getMessage();
                $results['errors'][] = $error;
                $this->error($error);
            }
        }

        return $results;
    }

    /**
     * Clean up Docker resources for a project
     */
    private function cleanupDockerResources(Project $project, bool $isDryRun): array
    {
        $results = [
            'containers_removed' => 0,
            'images_removed' => 0,
            'errors' => [],
        ];

        $this->line('  🐳 Cleaning Docker resources...');

        try {
            // Stop and remove containers
            $containers = $project->containers;
            foreach ($containers as $container) {
                if ($container->container_id) {
                    if (! $isDryRun) {
                        // Check if container is running
                        $statusResult = Process::run("docker ps -q --filter \"name={$container->container_id}\"");
                        $isRunning = $statusResult->successful() && trim($statusResult->output());

                        if ($isRunning) {
                            $this->line("    🛑 Stopping running container: {$container->container_id}");
                            // Stop container with timeout
                            $stopResult = Process::timeout(30)->run("docker stop {$container->container_id}");
                            if ($stopResult->successful()) {
                                $this->line("    ✅ Stopped container: {$container->container_id}");
                                // Wait a moment for container to fully stop
                                sleep(1);
                            } else {
                                $this->warn("    ⚠️  Container didn't stop gracefully, force killing...");
                                $killResult = Process::run("docker kill {$container->container_id}");
                                if ($killResult->successful()) {
                                    $this->line("    ✅ Force killed container: {$container->container_id}");
                                }
                            }
                        } else {
                            $this->line("    ℹ️  Container {$container->container_id} is not running");
                        }

                        // Remove container (force remove to handle any state)
                        $removeResult = Process::run("docker rm -f {$container->container_id}");
                        if ($removeResult->successful()) {
                            $this->line("    ✅ Removed container: {$container->container_id}");
                            $results['containers_removed']++;
                        } else {
                            $error = $removeResult->errorOutput();
                            if (strpos($error, 'No such container') === false) {
                                $results['errors'][] = "Failed to remove container {$container->container_id}: ".$error;
                            } else {
                                $this->line("    ℹ️  Container {$container->container_id} already removed");
                                $results['containers_removed']++;
                            }
                        }
                    } else {
                        // Check if container would be running in dry run
                        $statusResult = Process::run("docker ps -q --filter \"name={$container->container_id}\"");
                        $isRunning = $statusResult->successful() && trim($statusResult->output());

                        if ($isRunning) {
                            $this->line("    🔍 Would stop running container: {$container->container_id}");
                        } else {
                            $this->line("    🔍 Would remove stopped container: {$container->container_id}");
                        }
                        $results['containers_removed']++;
                    }
                }
            }

            // Remove Docker images
            $imageName = "lovable-project-{$project->id}";
            if (! $isDryRun) {
                $imageResult = Process::run("docker rmi {$imageName}");
                if ($imageResult->successful()) {
                    $this->line("    ✅ Removed image: {$imageName}");
                    $results['images_removed']++;
                } elseif (strpos($imageResult->errorOutput(), 'No such image') === false) {
                    $results['errors'][] = "Failed to remove image {$imageName}: ".$imageResult->errorOutput();
                }
            } else {
                $this->line("    🔍 Would remove image: {$imageName}");
                $results['images_removed']++;
            }

        } catch (\Exception $e) {
            $error = "Docker cleanup error for project {$project->id}: ".$e->getMessage();
            $results['errors'][] = $error;
            $this->error("    ❌ {$error}");
        }

        return $results;
    }

    /**
     * Clean up project files
     */
    private function cleanupProjectFiles(Project $project, bool $isDryRun): array
    {
        $results = [
            'files_removed' => 0,
            'errors' => [],
        ];

        $this->line('  📁 Cleaning project files...');

        try {
            $projectDir = storage_path("app/projects/{$project->id}");

            if (is_dir($projectDir)) {
                if (! $isDryRun) {
                    // Count files before deletion
                    $fileCount = count(File::allFiles($projectDir));

                    // Remove directory and all contents
                    if (File::deleteDirectory($projectDir)) {
                        $this->line("    ✅ Removed project directory: {$projectDir}");
                        $results['files_removed'] = $fileCount;
                    } else {
                        $results['errors'][] = "Failed to remove project directory: {$projectDir}";
                    }
                } else {
                    $fileCount = is_dir($projectDir) ? count(File::allFiles($projectDir)) : 0;
                    $this->line("    🔍 Would remove project directory: {$projectDir} ({$fileCount} files)");
                    $results['files_removed'] = $fileCount;
                }
            } else {
                $this->line("    ℹ️  Project directory does not exist: {$projectDir}");
            }

        } catch (\Exception $e) {
            $error = "File cleanup error for project {$project->id}: ".$e->getMessage();
            $results['errors'][] = $error;
            $this->error("    ❌ {$error}");
        }

        return $results;
    }

    /**
     * Clean up database records
     */
    private function cleanupDatabaseRecords(Project $project, bool $isDryRun): array
    {
        $results = [
            'records_removed' => 0,
            'errors' => [],
        ];

        $this->line('  🗄️  Cleaning database records...');

        try {
            if (! $isDryRun) {
                // Count records before deletion
                $containerCount = $project->containers()->count();
                $promptCount = $project->prompts()->count();
                $totalRecords = $containerCount + $promptCount + 1; // +1 for the project itself

                // Delete related records first (due to foreign key constraints)
                $project->containers()->delete();
                $project->prompts()->delete();

                // Delete the project
                $project->delete();

                $this->line('    ✅ Removed project and related records');
                $results['records_removed'] = $totalRecords;
            } else {
                $containerCount = $project->containers()->count();
                $promptCount = $project->prompts()->count();
                $totalRecords = $containerCount + $promptCount + 1;

                $this->line("    🔍 Would remove project and {$totalRecords} related records");
                $results['records_removed'] = $totalRecords;
            }

        } catch (\Exception $e) {
            $error = "Database cleanup error for project {$project->id}: ".$e->getMessage();
            $results['errors'][] = $error;
            $this->error("    ❌ {$error}");
        }

        return $results;
    }

    /**
     * Show cleanup results
     */
    private function showResults(array $results, bool $isDryRun): void
    {
        $this->newLine();
        $this->info('📊 Cleanup Results:');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Projects Processed', $results['projects_processed']],
                ['Containers Removed', $results['containers_removed']],
                ['Images Removed', $results['images_removed']],
                ['Files Removed', $results['files_removed']],
                ['Database Records Removed', $results['database_records_removed']],
                ['Errors', count($results['errors'])],
            ]
        );

        if (! empty($results['errors'])) {
            $this->newLine();
            $this->error('❌ Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("  • {$error}");
            }
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn('🔍 This was a dry run - no actual changes were made.');
            $this->info('Run the command without --dry-run to perform the actual cleanup.');
        } else {
            $this->newLine();
            $this->info('✅ Cleanup completed successfully!');
        }
    }
}
