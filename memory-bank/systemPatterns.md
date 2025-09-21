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

### 2. AI Website Generation Flow
```
User Prompt → AI Provider Selection (Claude first, then OpenAI) → AI Analysis → 
Website Type Detection → Next.js Project Generation → Code Storage → Project Ready
```

### 3. Docker Container Management Flow
```
Project with Generated Code → Docker Container Creation → Next.js Dev Server → 
Port Allocation → Live Preview URL → Real-time Website Preview
```

### 4. AI Provider Fallback Pattern
```
Claude AI (Primary - 3.7x cheaper) → OpenAI (Backup) → Mock Generation (Fallback)
```

### 5. Docker Container Lifecycle Pattern
```
Container Creation → Next.js Dev Server → Port Mapping → Live Preview → 
Health Monitoring → Resource Cleanup → Container Termination
```

### 6. Project Management Flow
```
CRUD Operations → Validation → Database Updates → Real-time UI Updates
```

### 7. Cost Optimization Pattern
```
Token Usage Tracking → Cost Calculation → Provider Selection → Budget Management
```

### 8. Sandbox Interface Flow
```
Project Selection → Deployment Modal → Progress Tracking → Sandbox Redirect → 
Interactive Development (Chat, Console, Code Editor) → File Management → AI Integration
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
- **Base Image**: Node.js 18 Alpine for Next.js projects
- **Development Mode**: Uses `npm run dev` for live previews
- **No nginx Required**: Next.js built-in server handles everything
- **Port Mapping**: Dynamic port allocation (8000+ range)

### 2. Container Lifecycle Management
- **Creation**: Dockerfile generation based on project type
- **Startup**: Next.js development server with hot reload
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
