# Cursor CLI Integration Guide

## Overview

This guide documents the integration of **Cursor CLI** and **choose** functionality into the Lovable Clone AI-powered website builder.

## Features Added

### 1. Cursor CLI AI Provider
- **New AI Model**: "Cursor CLI" added to the model selection interface
- **Terminal Integration**: Command-line interface for Cursor's AI agent
- **Fallback Support**: Integrated into the existing AI provider fallback system

### 2. Enhanced Console Commands

#### Cursor CLI Commands
```bash
# Start Cursor CLI agent
cursor

# Show help
cursor --help

# Show version
cursor --version

# Install Cursor CLI
cursor --install

# Generate code from prompt
cursor "create a todo component"
```

#### Choose Command for Interactive Selections
```bash
# Show main menu
choose

# Select AI models
choose 1
choose models
choose ai

# Select project types
choose 2
choose types
choose projects

# Select commands
choose 3
choose commands
choose cmd

# Select file templates
choose 4
choose templates
choose files

# Select specific options
choose model 1    # Select Claude Code
choose type 2     # Select Vite + React
```

### 3. Laravel Artisan Commands

#### Install Cursor CLI
```bash
# Install Cursor CLI
php artisan cursor:install

# Check installation status
php artisan cursor:install --check

# Force reinstallation
php artisan cursor:install --force
```

## AI Provider Integration

### Provider Hierarchy
1. **Claude AI** (Primary - 3.7x cheaper)
2. **OpenAI** (Secondary - reliable fallback)
3. **Gemini** (Tertiary - Google's model)
4. **Cursor CLI** (Quaternary - terminal-based)
5. **Mock Generation** (Fallback - guaranteed availability)

### Cursor CLI Service Features
- **Automatic Detection**: Checks if Cursor CLI is installed
- **Installation Support**: Built-in installation functionality
- **JSON Response Parsing**: Handles mixed output formats
- **Token Estimation**: Estimates token usage for cost tracking
- **Error Handling**: Comprehensive error handling with fallbacks

## Frontend Integration

### Model Selection Component
- **Updated Interface**: Cursor CLI now appears as an available option
- **Status Indicators**: Shows "READY" status when Cursor CLI is available
- **Visual Design**: Consistent with existing model cards

### Sandbox Console Enhancement
- **New Commands**: `cursor` and `choose` commands added
- **Interactive Menus**: Dynamic selection interfaces
- **Command History**: Full history support for all new commands
- **Help System**: Comprehensive help for all commands

## Usage Examples

### 1. Using Cursor CLI for Code Generation

```typescript
// In the sandbox console:
cursor "create a responsive navbar component with dark mode support"

// The AI will generate:
// - Component files
// - TypeScript definitions
// - Styling with Tailwind CSS
// - Dark mode implementation
```

### 2. Interactive Model Selection

```bash
# In the sandbox console:
choose models

# Output:
🤖 AI Models Selection:
  ▶ 1) Claude Code (Active) - Anthropic's advanced coding model
    2) OpenAI GPT-4 - OpenAI's flagship model  
    3) Cursor CLI - Terminal-based AI agent
    4) Gemini - Google's AI model

# Select Cursor CLI:
choose model 3

# Output:
✅ Selected AI Model: Cursor CLI
Model selection updated for next generation.
```

### 3. Project Type Selection

```bash
choose types

# Output:
📁 Project Types:
  ▶ 1) Next.js - React framework with SSR
    2) Vite + React - Fast build tool with React
    3) SvelteKit - Svelte framework with TypeScript

choose type 2

# Output:
✅ Selected Project Type: Vite + React
Project type updated for next generation.
```

## Installation Process

### Automatic Installation
```bash
# Install via Artisan command
php artisan cursor:install

# The command will:
# 1. Check if already installed
# 2. Download the installer
# 3. Run installation script
# 4. Verify installation
# 5. Show installation details
```

### Manual Installation
```bash
# If automatic installation fails:
curl https://cursor.com/install -fsS | bash

# Add to PATH (if not already added):
echo 'export PATH="$HOME/.local/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc

# Verify installation:
which cursor-agent
cursor-agent --version
```

### Authentication Setup
```bash
# Cursor CLI requires authentication
cursor-agent login

# Check authentication status
cursor-agent status

# Set API key (alternative to login)
export CURSOR_API_KEY="your-api-key"
```

## Technical Implementation

### Backend Services

#### CursorAIService
- **Location**: `app/Services/CursorAIService.php`
- **Methods**: 
  - `generateWebsite()` - Generate projects using Cursor CLI
  - `isCursorCliAvailable()` - Check CLI availability
  - `installCursorCli()` - Install CLI automatically
  - `isConfigured()` - Check if service is ready

#### InstallCursorCliCommand
- **Location**: `app/Console/Commands/InstallCursorCliCommand.php`
- **Features**:
  - Interactive installation process
  - Progress tracking
  - Installation verification
  - Error handling

### Frontend Components

#### ModelSelection Component
- **Location**: `resources/js/components/sections/ModelSelection.tsx`
- **Enhancement**: Added Cursor CLI as available model option

#### Sandbox Console
- **Location**: `resources/js/pages/projects/Sandbox.tsx`
- **Enhancements**:
  - `handleCursorCommand()` - Process cursor commands
  - `handleChooseCommand()` - Interactive selection menus
  - Extended help system
  - Command history support

## Testing

### Unit Tests
- **CursorAIServiceTest**: Tests all service methods
- **Process Mocking**: Tests CLI interactions
- **Error Handling**: Tests failure scenarios

### Feature Tests  
- **CursorCliInstallationTest**: Tests installation command
- **Console Integration**: Tests sandbox commands
- **End-to-End**: Tests complete workflow

## Configuration

### Environment Variables
```env
# Cursor CLI is automatically detected
# No additional configuration required
```

### Service Configuration
```php
// The service automatically configures itself
// based on CLI availability
```

## Benefits

### For Developers
- **Terminal Integration**: Use familiar command-line workflows
- **Multiple AI Options**: Choose the best AI for each task
- **Interactive Selection**: Easy switching between models and types
- **Comprehensive Help**: Built-in documentation and examples

### For Users
- **Enhanced UI**: More AI model options in the interface
- **Better Console**: Interactive commands with rich feedback
- **Reliable Fallbacks**: System never fails completely
- **Cost Optimization**: Smart provider selection based on cost

## Troubleshooting

### Common Issues

#### Cursor CLI Not Found
```bash
# Check if installed:
php artisan cursor:install --check

# Install if missing:
php artisan cursor:install
```

#### Permission Issues
```bash
# May need to restart terminal after installation
# or update PATH manually
```

#### Command Not Working
```bash
# Check console help:
help

# Verify cursor commands:
cursor --help
```

## Future Enhancements

### Planned Features
1. **Real-time Collaboration**: Cursor CLI in team environments
2. **Advanced Prompting**: Template-based prompt generation
3. **Custom Commands**: User-defined command shortcuts
4. **Integration API**: REST API for external integrations

### Potential Improvements
1. **GUI Installation**: Visual installation interface
2. **Model Preferences**: User-specific default models
3. **Command Aliases**: Shortened command names
4. **Batch Operations**: Multiple command execution

## Conclusion

The Cursor CLI integration significantly enhances the Lovable Clone platform by:

- **Expanding AI Options**: Four AI providers with intelligent fallbacks
- **Improving UX**: Interactive console with rich command support  
- **Increasing Reliability**: Multiple fallback mechanisms
- **Enhancing Productivity**: Terminal-based workflows for developers

This integration maintains the platform's commitment to providing a comprehensive, reliable, and user-friendly AI-powered website builder while adding powerful new capabilities for advanced users.
