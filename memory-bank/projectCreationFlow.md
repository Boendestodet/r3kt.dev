# Project Creation Flow: Lovable Clone

## Overview
The project creation flow is a comprehensive system that handles the complete lifecycle of creating a new AI-powered website project, from user input to sandbox deployment. It includes real-time validation, progress tracking, verification systems, and robust error handling.

## Flow Architecture

### 1. User Input Phase
- **Modal Interface**: Non-intrusive modal-based project creation
- **Form Fields**: Project name, description, AI prompt, model selection, stack selection
- **Real-time Validation**: Debounced API calls for project name uniqueness
- **Input Validation**: Client-side and server-side validation with clear error messages

### 2. Project Creation Phase
- **Database Setup**: Creates project record in database with user ownership
- **File System Setup**: Creates project folder in `@projects/{id}` with required files
- **Required Files**: `package.json`, `next.config.js`, `tsconfig.json`, `Dockerfile`, `docker-compose.yml`, `.dockerignore`
- **Progress Tracking**: Visual progress indicators with status messages

### 3. Verification Phase
- **Database Verification**: Confirms project exists in database
- **Folder Verification**: Confirms project folder exists in file system
- **File Verification**: Checks all required files are present
- **Status Assessment**: Success, partial, or failed verification with detailed feedback

### 4. AI Generation Phase
- **Prompt Processing**: Sends user prompt to AI service
- **Code Generation**: Generates Next.js project code using AI
- **Fallback Handling**: Graceful fallback if AI generation fails
- **Progress Updates**: Real-time status updates during generation

### 5. Docker Deployment Phase
- **Container Creation**: Creates Docker container for the project
- **Port Allocation**: Assigns available port for live preview
- **Service Startup**: Starts Next.js development server
- **Health Monitoring**: Monitors container health and status

### 6. Sandbox Redirect Phase
- **Completion**: Final progress update and success message
- **Redirect**: Automatic redirect to sandbox interface
- **State Cleanup**: Clears creation state and resets modal

## Technical Implementation

### Frontend Components

#### Project Creation Modal
```typescript
// Modal state management
const [showNewProjectModal, setShowNewProjectModal] = useState(false)
const [isCreatingProject, setIsCreatingProject] = useState(false)
const [creationProgress, setCreationProgress] = useState(0)
const [creationStatus, setCreationStatus] = useState("")
const [creationState, setCreationState] = useState<CreationState | null>(null)
```

#### Real-time Validation
```typescript
// Debounced project name checking
const checkProjectName = async (name: string) => {
  if (!name.trim()) {
    setNameError("")
    return
  }
  
  setIsCheckingName(true)
  try {
    const response = await fetch(`/api/projects/check-name?name=${encodeURIComponent(name)}`)
    const data = await response.json()
    if (data.exists) {
      setNameError("A project with this name already exists. Please choose a different name.")
    } else {
      setNameError("")
    }
  } catch (error) {
    console.error('Error checking project name:', error)
  } finally {
    setIsCheckingName(false)
  }
}
```

#### State Persistence
```typescript
// LocalStorage-based state recovery
const saveCreationState = (state: CreationState | null) => {
  if (state) {
    localStorage.setItem('projectCreationState', JSON.stringify(state))
  } else {
    localStorage.removeItem('projectCreationState')
  }
}

// Resume creation on page load
useEffect(() => {
  const savedState = localStorage.getItem('projectCreationState')
  if (savedState) {
    try {
      const state = JSON.parse(savedState)
      if (state.isActive) {
        // Resume creation flow
        setCreationState(state)
        setIsCreatingProject(true)
        // ... resume logic
      }
    } catch (error) {
      localStorage.removeItem('projectCreationState')
    }
  }
}, [])
```

### Backend Implementation

#### Project Controller
```php
public function store(StoreProjectRequest $request)
{
    $project = auth()->user()->projects()->create([
        'name' => $request->name,
        'description' => $request->description,
        'slug' => Str::slug($request->name),
        'settings' => $request->settings ?? [],
    ]);

    $this->setupProjectFiles($project);

    // Return Inertia response with project data
    if (request()->header('X-Inertia')) {
        return Inertia::render('projects/Index', [
            'projects' => auth()->user()->projects()
                ->with(['containers', 'prompts'])
                ->latest()
                ->paginate(12),
            'createdProject' => $project,
        ]);
    }
    // ... other response types
}
```

#### Project Verification
```php
public function verifySetup(Project $project)
{
    $verification = [
        'database_exists' => false,
        'folder_exists' => false,
        'required_files' => [],
        'all_files_present' => false,
        'overall_status' => 'failed'
    ];

    // Check database existence
    $verification['database_exists'] = $project->exists;

    // Check folder existence
    $projectPath = storage_path("app/projects/{$project->id}");
    $verification['folder_exists'] = is_dir($projectPath);

    // Check required files
    $requiredFiles = [
        'package.json', 'next.config.js', 'tsconfig.json',
        'Dockerfile', 'docker-compose.yml', '.dockerignore'
    ];

    $filesPresent = [];
    foreach ($requiredFiles as $file) {
        $filePath = $projectPath . '/' . $file;
        $filesPresent[$file] = file_exists($filePath);
    }

    $verification['required_files'] = $filesPresent;
    $verification['all_files_present'] = !in_array(false, $filesPresent);

    // Determine overall status
    if ($verification['database_exists'] && $verification['folder_exists'] && $verification['all_files_present']) {
        $verification['overall_status'] = 'success';
    } elseif ($verification['database_exists'] && $verification['folder_exists']) {
        $verification['overall_status'] = 'partial';
    } else {
        $verification['overall_status'] = 'failed';
    }

    return response()->json(['verification' => $verification]);
}
```

## Progress Flow

### Step 1: Project Creation (1-20%)
- **1-10%**: "Creating project and setting up the docker container"
- **10%**: "Checking container" (verification step)
- **15%**: "Project verified successfully! Generating code..."
- **20%**: "Generating the code..."

### Step 2: AI Generation (20-50%)
- **20-50%**: "Generating the code..."
- **50%**: "Code generation complete! Deploying to Docker..."

### Step 3: Docker Deployment (50-70%)
- **50-70%**: "Deploying to Docker..."
- **70%**: "Checking status..."

### Step 4: Container Startup (70-95%)
- **70-95%**: "Starting the project..."
- **95%**: "Redirect to sandbox..."

### Step 5: Completion (95-100%)
- **95-100%**: "Project created and deployed successfully!"
- **100%**: Redirect to sandbox interface

## Error Handling

### Client-side Error Handling
- **Network Errors**: Graceful handling of API failures
- **Validation Errors**: Clear error messages for form validation
- **Timeout Handling**: Fallback mechanisms for long-running operations
- **State Recovery**: Automatic recovery from interrupted creation flows

### Server-side Error Handling
- **Database Errors**: Proper error logging and user feedback
- **File System Errors**: Graceful handling of file creation failures
- **AI Service Errors**: Fallback to mock generation if AI fails
- **Docker Errors**: Proper cleanup and error reporting

## Security Considerations

### CSRF Protection
- **Token Validation**: Proper CSRF token handling for all requests
- **Token Retrieval**: Robust token retrieval from meta tags, cookies, or window objects
- **Request Validation**: Server-side validation of all incoming requests

### Input Validation
- **Client-side**: Real-time validation with immediate feedback
- **Server-side**: Comprehensive validation using Laravel Form Requests
- **Sanitization**: Proper input sanitization and validation

### Authorization
- **User Ownership**: Projects are tied to authenticated users
- **Access Control**: Proper authorization checks for all operations
- **Data Isolation**: User data is properly isolated and protected

## Performance Optimizations

### Frontend Optimizations
- **Debounced Validation**: Prevents excessive API calls during typing
- **State Persistence**: Efficient LocalStorage usage for state recovery
- **Progress Updates**: Smooth progress updates without blocking UI
- **Error Recovery**: Fast error recovery and retry mechanisms

### Backend Optimizations
- **Database Queries**: Optimized queries with proper relationships
- **File Operations**: Efficient file system operations
- **Caching**: Strategic caching for expensive operations
- **Resource Cleanup**: Proper cleanup of temporary resources

## Testing Strategy

### Unit Tests
- **Validation Logic**: Test all validation rules and error cases
- **State Management**: Test state persistence and recovery
- **Error Handling**: Test all error scenarios and fallbacks

### Integration Tests
- **API Endpoints**: Test all project creation endpoints
- **File Operations**: Test file creation and verification
- **Database Operations**: Test database interactions and relationships

### End-to-End Tests
- **Complete Flow**: Test entire project creation flow
- **Error Scenarios**: Test error handling and recovery
- **User Experience**: Test user interaction and feedback

## Future Enhancements

### Planned Features
- **Batch Creation**: Support for creating multiple projects at once
- **Template System**: Pre-built project templates
- **Advanced Validation**: More sophisticated validation rules
- **Progress Persistence**: More detailed progress tracking

### Potential Improvements
- **Real-time Updates**: WebSocket-based real-time progress updates
- **Background Processing**: Queue-based project creation
- **Advanced Error Recovery**: More sophisticated error recovery mechanisms
- **Performance Monitoring**: Detailed performance metrics and monitoring
