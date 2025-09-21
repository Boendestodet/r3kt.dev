# Technical Context: Lovable Clone

## Technology Stack

### Backend Technologies
- **PHP**: 8.3.24 (Latest stable with performance improvements)
- **Laravel**: 12.26.4 (Latest with new features and optimizations)
- **Database**: MySQL (Reliable, well-supported relational database)
- **Queue System**: Laravel Queues (Background job processing)

### Frontend Technologies
- **React**: 19.1.1 (Latest with concurrent features and improved performance)
- **TypeScript**: 5.7.2 (Type safety and better developer experience)
- **Inertia.js**: 2.1.2 (Seamless SPA experience with Laravel)
- **Tailwind CSS**: 4.1.12 (Latest with improved performance and new features)

### Build & Development Tools
- **Vite**: 7.0.4 (Fast build tool with HMR)
- **Laravel Wayfinder**: 0.1.10 (Enhanced routing and navigation)
- **ESLint**: 9.33.0 (Code quality and consistency)
- **Prettier**: 3.6.2 (Code formatting)
- **Pest**: 4.0.4 (Modern testing framework)

### UI Component Libraries
- **Radix UI**: Comprehensive accessible component primitives
- **Headless UI**: Unstyled, accessible UI components
- **Lucide React**: Beautiful icon library
- **Class Variance Authority**: Component variant management
- **Custom UI Components**: Reusable components (Textarea, Buttons, Modals)

### AI Integration
- **OpenAI GPT-4**: Primary AI provider for website generation
- **Claude 3.5 Sonnet**: Cost-optimized AI provider (3.7x cheaper)
- **Anthropic PHP Client**: Official Claude AI integration
- **OpenAI PHP Client**: Official OpenAI integration
- **Smart Fallback**: Automatic provider switching for reliability

### Docker Integration
- **Docker Engine**: Real container management for live previews
- **Next.js Development Server**: Built-in server (no nginx required)
- **Port Management**: Dynamic port allocation and conflict resolution
- **Resource Cleanup**: Automatic cleanup of old containers and images
- **Container Monitoring**: Health checks and resource usage tracking

### Sandbox Interface Technologies
- **React Hooks**: useState, useEffect for complex state management
- **Inertia.js Navigation**: Seamless page transitions and routing
- **Tailwind CSS v4**: Advanced styling with glow effects and animations
- **Custom Components**: Reusable UI components with TypeScript
- **Modal System**: Deployment progress tracking with visual feedback
- **Tab Interface**: Multi-panel development environment
- **Code Editor**: Syntax highlighting and file management
- **Console Interface**: Command history and interactive input
- **AI Chat Integration**: Real-time chat with prompt enhancement

## Development Environment

### Local Development
- **Server**: Laravel development server (`php artisan serve`)
- **Frontend**: Vite dev server with HMR (`npm run dev`)
- **Database**: MySQL with migrations and seeders
- **Queue**: Laravel queue worker for background jobs
- **Logs**: Laravel Pail for real-time log monitoring

### Development Scripts
```bash
# Full development environment
composer run dev

# Individual services
php artisan serve          # Backend server
npm run dev               # Frontend with HMR
php artisan queue:listen  # Queue worker
php artisan pail          # Log monitoring
```

## Project Structure

### Backend Structure (Laravel 12)
```
app/
├── Http/
│   ├── Controllers/     # API and web controllers
│   ├── Requests/        # Form validation classes
│   └── Middleware/      # Custom middleware
├── Models/              # Eloquent models
├── Services/            # Business logic services
├── Jobs/                # Background job classes
├── Policies/            # Authorization policies
└── Providers/           # Service providers
```

### Frontend Structure (React + TypeScript)
```
resources/js/
├── pages/               # Route components (Projects/Index, Projects/Sandbox)
├── components/          # Reusable UI components
│   ├── ui/              # Base UI components (Textarea, Buttons, etc.)
│   └── layout/          # Layout-specific components
├── layouts/             # Layout components
├── hooks/               # Custom React hooks
├── lib/                 # Utility functions (cn, utils)
├── types/               # TypeScript type definitions
└── wayfinder/           # Wayfinder configuration
```

## Database Schema

### Core Tables
- **users**: User authentication and profile data
- **projects**: Website projects with metadata
- **prompts**: User input prompts for website generation
- **containers**: Real Docker container management with live previews
- **comments**: Project comments and feedback

### Key Relationships
- Users have many projects
- Projects have many prompts
- Projects have one container
- Projects have many comments

## Configuration

### Environment Variables
- **Database**: MySQL connection settings
- **Queue**: Redis/database queue configuration
- **Mail**: SMTP settings for notifications
- **Storage**: File storage configuration
- **Security**: App key, session settings

### AI Provider Configuration
```env
# OpenAI Configuration
OPENAI_API_KEY=your_openai_api_key_here
OPENAI_MODEL=gpt-4
OPENAI_MAX_TOKENS=4000
OPENAI_TEMPERATURE=0.7

# Claude AI Configuration
CLAUDE_API_KEY=your_claude_api_key_here
CLAUDE_MODEL=claude-3-5-sonnet-20241022
CLAUDE_MAX_TOKENS=4000
CLAUDE_TEMPERATURE=0.7
```

### Cost Management
- **Token Tracking**: Automatic usage monitoring per request
- **Cost Estimation**: Real-time calculation before generation
- **Provider Selection**: Intelligent routing based on cost and availability
- **Budget Limits**: Configurable spending controls

### Docker Configuration
- **Container Base Image**: Node.js 18 Alpine
- **Development Mode**: `npm run dev` for live previews
- **Port Range**: 8000-8999 for dynamic allocation
- **Resource Limits**: Configurable CPU and memory constraints
- **Cleanup Policies**: Automatic removal of stopped containers
- **Health Checks**: Container status monitoring

### Build Configuration
- **Vite**: Asset bundling and optimization
- **TypeScript**: Compilation settings
- **ESLint**: Code quality rules
- **Prettier**: Code formatting rules
- **Tailwind**: CSS processing and purging

## Testing Strategy

### Test Types
- **Feature Tests**: End-to-end user workflows
- **Unit Tests**: Individual component testing
- **Browser Tests**: Real browser interaction testing (Pest v4)

### Test Coverage
- User authentication and authorization
- Project CRUD operations
- AI website generation logic
- API endpoints and responses
- Frontend component behavior

## Deployment Considerations

### Production Requirements
- **PHP**: 8.3+ with required extensions
- **Database**: MySQL 8.0+
- **Web Server**: Nginx or Apache
- **Node.js**: 18+ for asset building
- **Queue Worker**: Background job processing

### Performance Optimizations
- **Caching**: Redis for sessions and cache
- **Assets**: Vite production builds with optimization
- **Database**: Proper indexing and query optimization
- **CDN**: Static asset delivery

## Security Considerations

### Authentication & Authorization
- Laravel's built-in authentication system
- Policy-based authorization
- CSRF protection on all forms
- XSS prevention through React escaping

### Data Protection
- Input validation and sanitization
- SQL injection prevention via Eloquent
- Secure file upload handling
- Environment variable protection

## Monitoring & Logging

### Application Monitoring
- Laravel Pail for real-time logs
- Error tracking and reporting
- Performance monitoring
- User activity tracking

### Development Tools
- Laravel Boost for enhanced development
- Browser developer tools
- Database query monitoring
- Asset build monitoring
