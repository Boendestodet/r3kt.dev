# 🧪 Test Suite Documentation

## Overview

This project has two types of tests to balance speed, cost, and thoroughness:

1. **Mocked Tests** - Fast, free, no real API calls
2. **Real AI Tests** - Slower, costs money, tests actual AI integration

## 🚀 Quick Start

### Option 1: Use the Test Runner Script
```bash
./test-runner.sh
```

### Option 2: Run Commands Directly

**Mocked Tests (Recommended for development):**
```bash
php artisan test --exclude-group=real-ai
```

**Real AI Tests (When you want to test actual AI):**
```bash
php artisan test tests/Feature/RealAITest.php
```

**All Tests:**
```bash
php artisan test
```

## 📁 Test Structure

### Mocked Tests (Fast & Free)
- **Duration**: ~1-2 seconds
- **Cost**: $0
- **Files**: All tests except `RealAITest.php`
- **Purpose**: Test all functionality with mocked AI services

### Real AI Tests (Slow & Costs Money)
- **Duration**: ~30-60 seconds
- **Cost**: ~$0.01-0.05 per run
- **Files**: `tests/Feature/RealAITest.php`
- **Purpose**: Test actual AI integration and API responses

## 🔧 Configuration

### For Real AI Tests
Make sure these environment variables are set in your `.env`:
```env
ANTHROPIC_API_KEY=your_claude_api_key
OPENAI_API_KEY=your_openai_api_key
```

### Test Groups
- `mocked` - Tests using mocked services
- `real-ai` - Tests using real AI services
- `integration` - End-to-end tests
- `unit` - Unit tests

## 📊 Test Performance

| Test Type | Duration | Cost | API Calls | Use Case |
|-----------|----------|------|-----------|----------|
| Mocked | 1-2s | $0 | None | Development, CI/CD |
| Real AI | 30-60s | $0.01-0.05 | Yes | Integration testing |
| All | 1-2min | $0.01-0.05 | Yes | Full validation |

## 🎯 Best Practices

### Development Workflow
1. **During development**: Use mocked tests for fast feedback
2. **Before deployment**: Run real AI tests to verify integration
3. **CI/CD**: Use mocked tests for automated testing

### When to Use Real AI Tests
- ✅ Testing new AI features
- ✅ Verifying API integration changes
- ✅ Before production deployment
- ✅ Debugging AI-related issues

### When to Use Mocked Tests
- ✅ Regular development
- ✅ CI/CD pipelines
- ✅ Quick feature validation
- ✅ Cost-sensitive environments

## 🚨 Important Notes

- **Real AI tests cost money** - only run when necessary
- **Mocked tests are fast** - use for regular development
- **Test groups** help you run specific test types
- **Environment setup** required for real AI tests

## 🔍 Troubleshooting

### Real AI Tests Failing
- Check API keys are set in `.env`
- Verify API keys are valid and have credits
- Check network connectivity
- Review API rate limits

### Mocked Tests Failing
- Check test database setup
- Verify all dependencies are installed
- Review test data setup

## 📈 Test Coverage

- ✅ Auto-start container functionality
- ✅ Project CRUD operations
- ✅ Docker container management
- ✅ AI generation (mocked and real)
- ✅ User authentication
- ✅ Real-time collaboration
- ✅ Subdomain management
