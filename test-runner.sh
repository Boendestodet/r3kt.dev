#!/bin/bash

# Test Runner Script
# Helps you choose between different test modes

echo "🧪 Lovable Clone Test Runner"
echo "=============================="
echo ""
echo "Choose your test mode:"
echo "1) Mocked Tests (Fast, Free, No API calls)"
echo "2) Real AI Tests (Slow, Costs money, Real API calls)"
echo "3) Docker Container Tests (Tests Docker without AI, Free)"
echo "4) Live Docker Test (Creates real containers, shows results)"
echo "5) All Tests (Both mocked and real AI)"
echo "6) Specific test file"
echo "7) Show test configuration"
echo ""

read -p "Enter your choice (1-7): " choice

case $choice in
    1)
        echo "🚀 Running Mocked Tests (Fast & Free)..."
        echo "Duration: ~1-2 seconds | Cost: $0"
        echo ""
        php artisan test --exclude-group=real-ai
        ;;
    2)
        echo "🤖 Running Real AI Tests (Slow & Costs Money)..."
        echo "Duration: ~30-60 seconds | Cost: ~$0.01-0.05"
        echo "⚠️  WARNING: This will make real API calls!"
        echo ""
        read -p "Are you sure? (y/N): " confirm
        if [[ $confirm == [yY] ]]; then
            php artisan test tests/Feature/RealAITest.php
        else
            echo "❌ Cancelled"
        fi
        ;;
    3)
        echo "🐳 Running Docker Container Tests (Free, No AI)..."
        echo "Duration: ~10-30 seconds | Cost: $0"
        echo "Tests Docker container creation and management"
        echo ""
        php artisan test tests/Feature/DockerContainerTest.php
        ;;
    4)
        echo "🐳 Running Live Docker Test (Creates real containers)..."
        echo "Duration: ~1-2 seconds | Cost: $0"
        echo "Creates real Docker containers and shows results"
        echo ""
        php artisan test tests/Feature/LiveDockerTest.php
        ;;
    5)
        echo "🔄 Running All Tests (Both Mocked and Real AI)..."
        echo "Duration: ~1-2 minutes | Cost: ~$0.01-0.05"
        echo "⚠️  WARNING: This will make real API calls!"
        echo ""
        read -p "Are you sure? (y/N): " confirm
        if [[ $confirm == [yY] ]]; then
            php artisan test
        else
            echo "❌ Cancelled"
        fi
        ;;
    6)
        echo "📁 Available test files:"
        ls tests/Feature/*.php | sed 's/tests\/Feature\//  /' | sed 's/\.php//'
        echo ""
        read -p "Enter test file name (without .php): " testfile
        if [[ -f "tests/Feature/${testfile}.php" ]]; then
            echo "🧪 Running ${testfile}..."
            php artisan test "tests/Feature/${testfile}.php"
        else
            echo "❌ Test file not found: tests/Feature/${testfile}.php"
        fi
        ;;
    7)
        echo "📋 Test Configuration:"
        echo ""
        echo "Mocked Tests:"
        echo "  - Fast execution (~1-2 seconds)"
        echo "  - No API calls, no cost"
        echo "  - Tests all functionality with mocks"
        echo "  - Command: php artisan test --exclude-group=real-ai"
        echo ""
        echo "Real AI Tests:"
        echo "  - Slower execution (~30-60 seconds)"
        echo "  - Real API calls, costs money"
        echo "  - Tests actual AI integration"
        echo "  - Command: php artisan test tests/Feature/RealAITest.php"
        echo ""
        echo "Docker Container Tests:"
        echo "  - Tests Docker container creation and management (~10-30 seconds)"
        echo "  - No AI calls, no cost"
        echo "  - Requires Docker daemon running for full functionality"
        echo "  - Command: php artisan test tests/Feature/DockerContainerTest.php"
        echo ""
        echo "Live Docker Test:"
        echo "  - Creates real Docker containers and shows results (~1-2 seconds)"
        echo "  - No AI calls, no cost"
        echo "  - Demonstrates actual container creation"
        echo "  - Command: php artisan test tests/Feature/LiveDockerTest.php"
        echo ""
        echo "Requirements for Real AI Tests:"
        echo "  - ANTHROPIC_API_KEY in .env"
        echo "  - OPENAI_API_KEY in .env"
        echo "  - Docker daemon running (for container tests)"
        ;;
    *)
        echo "❌ Invalid choice"
        ;;
esac
