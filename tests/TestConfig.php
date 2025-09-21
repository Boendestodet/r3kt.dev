<?php

/**
 * Test Configuration
 *
 * This file helps you choose between different test modes:
 *
 * 1. MOCKED TESTS (Default) - Fast, free, no real API calls
 * 2. REAL AI TESTS - Slower, costs money, tests actual AI integration
 * 3. MIXED TESTS - Some mocked, some real
 */

return [
    // Test Modes
    'modes' => [
        'mocked' => [
            'description' => 'Fast tests with mocked AI services (no API calls, no cost)',
            'files' => [
                'tests/Feature/SimpleAutoStartTest.php',
                'tests/Feature/ProjectTest.php',
                'tests/Feature/DockerManagementTest.php',
                'tests/Feature/AIWebsiteGeneratorTest.php',
                'tests/Feature/AIMultiProviderTest.php',
                'tests/Feature/DeploymentTest.php',
                'tests/Feature/CollaborationTest.php',
                'tests/Feature/CommentTest.php',
                'tests/Feature/SubdomainTest.php',
                'tests/Feature/DashboardTest.php',
                'tests/Feature/GalleryTest.php',
            ],
            'command' => 'php artisan test --exclude-group=real-ai',
            'duration' => '~1-2 seconds',
            'cost' => '$0',
        ],

        'real-ai' => [
            'description' => 'Real AI integration tests (API calls, costs money)',
            'files' => [
                'tests/Feature/RealAITest.php',
            ],
            'command' => 'php artisan test tests/Feature/RealAITest.php',
            'duration' => '~30-60 seconds',
            'cost' => '~$0.01-0.05 per test run',
        ],

        'all' => [
            'description' => 'All tests including real AI (comprehensive but expensive)',
            'files' => 'All test files',
            'command' => 'php artisan test',
            'duration' => '~1-2 minutes',
            'cost' => '~$0.01-0.05 per test run',
        ],
    ],

    // Environment Requirements for Real AI Tests
    'real_ai_requirements' => [
        'claude_api_key' => 'ANTHROPIC_API_KEY in .env',
        'openai_api_key' => 'OPENAI_API_KEY in .env',
        'docker_available' => 'Docker daemon running (for container tests)',
    ],

    // Test Groups
    'groups' => [
        'mocked' => 'Tests that use mocked services',
        'real-ai' => 'Tests that use real AI services',
        'integration' => 'End-to-end integration tests',
        'unit' => 'Unit tests for individual components',
    ],
];
