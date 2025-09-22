# System Patterns: Lovable Clone

## Architecture Overview
The application follows a modern full-stack architecture with clear separation of concerns:

```
Frontend (React 19 + TypeScript)
    ↕ (Inertia.js v2)
Backend (Laravel 12 + PHP 8.3)
    ↕ (Eloquent ORM)
Database (MySQL)
```

## Key Design Patterns

### 1. MVC Architecture (Laravel)
- **Models**: `User`, `Project`, `Prompt`, `Comment`, `Container`
- **Controllers**: Handle HTTP requests and business logic
- **Views**: React components rendered via Inertia.js

### 2. Service Layer Pattern
- **AIWebsiteGeneratorService**: Handles prompt analysis and website generation with dual AI providers
- **OpenAIService**: Manages OpenAI GPT-4 API integration
- **ClaudeAIService**: Manages Claude 3.5 Sonnet API integration
- **AICostEstimator**: Provides cost estimation for AI operations
- **ProjectService**: Manages project operations and business logic
- **DockerService**: Manages real Docker container lifecycle with live previews

### 3. Repository Pattern (via Eloquent)
- Models act as repositories with built-in query methods
- Relationships defined for data integrity
- Scopes for common query patterns

### 4. Component-Based Architecture (React)
- **Pages**: Top-level route components
- **Components**: Reusable UI elements
- **Layouts**: Shared layout structures
- **Hooks**: Custom logic for state management

## Data Flow Patterns

### 1. User Authentication Flow
```
Registration/Login → User Model → Session Management → Protected Routes
```

### 2. AI Website Generation Flow ✅ FULLY WORKING
```
User Prompt → AI Provider Selection (Claude first, then OpenAI) → AI Analysis → 
Website Type Detection → Stack Selection (Next.js or Vite) → Project Generation → 
Code Storage → Configuration File Protection → Project Ready

Current Status:
- ✅ Claude Code working with Next.js and Vite + React
- ✅ OpenAI working with Next.js and Vite + React  
- ✅ Token management optimized (4000 tokens)
- ✅ Real AI generation (no more mock fallback)
- ✅ Prompt following (AI follows user prompts exactly)
```

### 3. Docker Container Management Flow
```
Project with Generated Code → Stack Detection (Next.js/Vite) → Docker Container Creation → 
Development Server (Next.js/Vite) → Port Allocation → Live Preview URL → Real-time Website Preview
```

### 4. AI Provider Fallback Pattern
```
Claude AI (Primary - 3.7x cheaper) → OpenAI (Backup) → Mock Generation (Fallback)
```

### 5. Docker Container Lifecycle Pattern
```
Container Creation → Stack Detection → Dev Server (Next.js/Vite) → Port Mapping → 
Live Preview → Health Monitoring → Resource Cleanup → Container Termination
```

### 6. Container Naming Pattern
```
Project Creation → Container ID Generation → Unique Name Creation → 
Database Storage → Docker Container Creation → Live Preview
```

### 7. Project Management Flow
```
CRUD Operations → Validation → Database Updates → Real-time UI Updates
```

### 8. Cost Optimization Pattern
```
Token Usage Tracking → Cost Calculation → Provider Selection → Budget Management
```

### 9. Sandbox Interface Flow
```
Project Selection → Deployment Modal → Progress Tracking → Sandbox Redirect → 
Interactive Development (Chat, Console, Code Editor) → File Management → AI Integration
```

### 10. Project Creation Flow
```
User Input → Project Name Validation → Project Creation → Database Setup → 
File System Setup → Project Verification → AI Code Generation → Wait for AI Completion → 
Docker Deployment → Container Startup → Sandbox Redirect
```

### 11. Project Verification Pattern
```
Database Check → Folder Existence Check → Required Files Check → 
Overall Status Assessment → Success/Partial/Failed Response → Continue/Stop Flow
```

### 12. Configuration File Protection Pattern
```
AI Generation → File Filtering → Protected Files Blocked → System Files Created → 
Docker Configuration → Container Deployment → Live Preview
```

### 13. Multi-Stack Project Creation Pattern
```
User Input → Stack Selection (Next.js/Vite) → Project Type Detection → 
Stack-Specific File Creation → Configuration Protection → Docker Setup → Live Preview
```

## AI Integration Patterns

### 1. Multi-Provider Architecture
- **Primary Provider**: Claude 3.5 Sonnet (cost-optimized)
- **Backup Provider**: OpenAI GPT-4 (reliability)
- **Fallback Provider**: Mock generation (guaranteed availability)

### 2. Cost Optimization Strategy
- **Provider Selection**: Claude tried first due to 3.7x lower cost
- **Token Tracking**: Complete usage monitoring per request
- **Cost Estimation**: Real-time cost calculation before generation
- **Budget Management**: Configurable spending limits

### 3. Error Handling & Resilience
- **Graceful Degradation**: System never fails completely
- **Retry Logic**: Automatic fallback between providers
- **Error Logging**: Comprehensive error tracking and monitoring
- **User Feedback**: Clear status updates during generation

### 4. Response Processing
- **JSON Validation**: Ensures valid Next.js project structure
- **Content Sanitization**: Clean, production-ready code
- **File Organization**: Proper project structure with all necessary files
- **Metadata Tracking**: Model used, tokens consumed, processing time

## Docker Container Patterns

### 1. Container Architecture
- **Base Image**: Node.js 18 Alpine for both Next.js and Vite projects
- **Development Mode**: Uses `npm run dev` for live previews
- **No nginx Required**: Built-in development servers handle everything
- **Port Mapping**: Dynamic port allocation (Next.js: 3000, Vite: 5173, mapped to 8000+ range)
- **Stack Detection**: Automatic detection of project type for appropriate server configuration

### 2. Container Lifecycle Management
- **Creation**: Dockerfile generation based on project type (Next.js or Vite)
- **Startup**: Development server with hot reload (Next.js or Vite)
- **Monitoring**: Health checks and resource usage tracking
- **Cleanup**: Automatic resource cleanup and container removal

### 3. Port Management Strategy
- **Dynamic Allocation**: Automatic port assignment starting from 8000
- **Conflict Resolution**: Checks for port availability before assignment
- **Port Tracking**: Database storage of assigned ports per container
- **URL Generation**: Dynamic preview URL generation with port mapping

### 4. Resource Management
- **Container Limits**: Configurable resource constraints
- **Cleanup Policies**: Automatic removal of stopped containers
- **Image Management**: Docker image cleanup and optimization
- **Storage Management**: Project file cleanup and organization

### 5. Error Handling & Resilience
- **Graceful Degradation**: Fallback to mock when Docker unavailable
- **Container Recovery**: Automatic restart on failure
- **Resource Monitoring**: CPU, memory, and disk usage tracking
- **Log Management**: Centralized logging and error reporting

### 6. Multi-Stack Configuration Management
- **Stack Detection**: Automatic detection based on project settings
- **Configuration Protection**: AI cannot overwrite critical system files
- **Independent Setups**: Separate configurations for Next.js and Vite projects
- **Docker Optimization**: Stack-specific Dockerfiles and server configurations

## Key Technical Decisions

### 1. Inertia.js for SPA Experience
- **Rationale**: Seamless integration between Laravel and React
- **Benefits**: No API layer needed, shared state management, SEO-friendly
- **Implementation**: Server-side rendering with client-side hydration

### 2. Tailwind CSS v4 for Styling
- **Rationale**: Utility-first approach for rapid development
- **Benefits**: Consistent design system, responsive by default
- **Implementation**: Component-based styling with design tokens

### 3. Pest v4 for Testing
- **Rationale**: Modern testing framework with excellent Laravel integration
- **Benefits**: Readable tests, built-in assertions, parallel execution
- **Implementation**: Feature tests for user flows, unit tests for business logic

### 4. Vite for Asset Bundling
- **Rationale**: Fast development and optimized production builds
- **Benefits**: Hot module replacement, tree shaking, modern JS features
- **Implementation**: TypeScript compilation, CSS processing, asset optimization

## Sandbox Interface Patterns

### 1. Modal-Based Deployment
- **Deployment Modal**: Progress tracking with visual feedback
- **State Management**: Loading states, progress updates, completion handling
- **User Experience**: Clear visual progression from deployment to sandbox

### 2. Tab-Based Interface
- **Chat Tab**: AI interaction with prompt enhancement
- **Console Tab**: Command-line interface with history navigation
- **Files Tab**: File explorer with code editor integration
- **Preview Tab**: Live project preview (future integration)

### 3. AI Chat Integration
- **Prompt Enhancement**: Intelligent prompt improvement before sending
- **Message History**: Persistent chat history with timestamps
- **Typing Indicators**: Real-time feedback during AI processing
- **Context Awareness**: AI responses based on project context
- **Auto-Resize Integration**: Seamless textarea resizing after enhanced prompts
- **Full Text Visibility**: Ensures enhanced prompts are fully visible without scrolling

### 4. Code Editor Patterns
- **File Management**: Tree-based file explorer with folder structure
- **Syntax Highlighting**: Basic syntax highlighting for code files
- **Auto-resize**: Dynamic textarea sizing based on content
- **Keyboard Shortcuts**: Ctrl+S for save, Ctrl+F for format
- **Line Numbers**: Professional code editor appearance

### 5. Console Interface
- **Command History**: Arrow key navigation through command history
- **Mock Commands**: Simulated command execution with responses
- **Interactive Input**: Real-time command input and execution
- **Status Feedback**: Clear command execution results

### 6. UI Component Patterns
- **Reusable Components**: Textarea component with custom styling
- **Glow Effects**: Elegant orange glow effects for visual appeal
- **Responsive Design**: Mobile-friendly interface with proper spacing
- **State Management**: React hooks for complex state management
- **Auto-Resize Logic**: Intelligent height management with multiple strategies
- **Dynamic Sizing**: Adaptive height based on content length (512px normal, 800px for long text)

### 7. Project Creation Patterns
- **Modal-Based Creation**: Non-intrusive project creation interface
- **Progress Tracking**: Visual progress indicators with status messages
- **State Persistence**: LocalStorage-based state recovery on page refresh
- **Real-time Validation**: Debounced API calls for project name uniqueness
- **Error Handling**: Comprehensive error messages and fallback mechanisms
- **Verification System**: Multi-step verification of database and file system setup
- **File Type Detection**: Smart detection of project type (Next.js vs HTML) for appropriate file creation
- **AI Generation Waiting**: Proper waiting for AI generation completion before container startup

## Component Relationships

### Backend Components
- **User Model**: Central to authentication and project ownership
- **Project Model**: Core entity with relationships to prompts and containers
- **AIWebsiteGeneratorService**: Business logic for website generation
- **Policies**: Authorization logic for user permissions

### Frontend Components
- **Layout Components**: Shared structure across pages
- **Page Components**: Route-specific functionality (Projects/Index, Projects/Sandbox)
- **UI Components**: Reusable elements with consistent styling (Textarea, Buttons)
- **Hooks**: Custom logic for state and side effects
- **Sandbox Components**: Interactive development environment components

## Data Relationships
```
User (1) → (Many) Projects
Project (1) → (Many) Prompts
Project (1) → (1) Container
Project (1) → (Many) Comments
```

## Security Patterns
- **Authentication**: Laravel's built-in auth system
- **Authorization**: Policies for user permissions
- **Validation**: Form requests for input validation
- **CSRF Protection**: Built-in Laravel CSRF tokens
- **XSS Prevention**: React's built-in escaping

## Performance Patterns
- **Eager Loading**: Prevent N+1 queries in Eloquent
- **Caching**: Strategic caching for expensive operations
- **Asset Optimization**: Vite for efficient bundling
- **Database Indexing**: Optimized queries for common operations

## Recent Fixes & Improvements

### 1. File Creation Logic Fix
- **Issue**: Both HTML and Next.js files were being created for Next.js projects
- **Solution**: Added project type detection to create only appropriate files
- **Pattern**: Smart file creation based on project settings
- **Impact**: Cleaner project structure, no confusion about file types

### 2. Container Start Timing Fix
- **Issue**: Container start failing due to missing generated_code
- **Solution**: Improved AI generation waiting logic with proper status checking
- **Pattern**: Asynchronous job completion waiting with fallback mechanisms
- **Impact**: Reliable container startup after AI generation

### 3. AI Generation Waiting Pattern
- **Issue**: Frontend not properly waiting for AI generation to complete
- **Solution**: Enhanced waiting logic with project data checking
- **Pattern**: Polling-based status checking with timeout handling
- **Impact**: Better user experience with proper timing

### 4. Error Handling Improvements
- **Issue**: Limited error handling in project creation flow
- **Solution**: Comprehensive error handling with clear user feedback
- **Pattern**: Graceful degradation with fallback mechanisms
- **Impact**: More robust system with better user experience

### 5. Multi-Stack Integration
- **Issue**: Only Next.js support, limited frontend framework options
- **Solution**: Added complete Vite + React + TypeScript support alongside Next.js
- **Pattern**: Stack detection and independent configuration management
- **Impact**: Users can choose between Next.js and Vite for their projects

### 6. Configuration File Protection
- **Issue**: AI could overwrite critical system configuration files
- **Solution**: Implemented file protection system with AI prompt restrictions
- **Pattern**: Protected file filtering and system-generated configuration override
- **Impact**: Reliable project setup with consistent Docker-compatible configurations

### 7. Container Naming System Fix
- **Issue**: Docker container naming causing duplicate entry errors in database
- **Solution**: Implemented unique container naming with project ID and container ID combination
- **Pattern**: `lovable-container-{project_id}-{container_id}` for unique identification
- **Impact**: Prevents database constraint violations and ensures proper container management
