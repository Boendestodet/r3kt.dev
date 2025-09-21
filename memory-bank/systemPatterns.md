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
- **AIWebsiteGeneratorService**: Handles prompt analysis and website generation
- **ProjectService**: Manages project operations and business logic
- **ContainerService**: Manages Docker container lifecycle (mocked)

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

### 2. Website Generation Flow
```
User Prompt → AI Analysis → Website Type Detection → Template Selection → Code Generation → Project Storage
```

### 3. Project Management Flow
```
CRUD Operations → Validation → Database Updates → Real-time UI Updates
```

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

## Component Relationships

### Backend Components
- **User Model**: Central to authentication and project ownership
- **Project Model**: Core entity with relationships to prompts and containers
- **AIWebsiteGeneratorService**: Business logic for website generation
- **Policies**: Authorization logic for user permissions

### Frontend Components
- **Layout Components**: Shared structure across pages
- **Page Components**: Route-specific functionality
- **UI Components**: Reusable elements with consistent styling
- **Hooks**: Custom logic for state and side effects

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
