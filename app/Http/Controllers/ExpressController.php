<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DockerService;
use App\Services\FilePermissionService;
use Illuminate\Support\Facades\Log;

class ExpressController extends Controller
{
    public function __construct(
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Generate Express.js specific system prompt
     */
    public function getSystemPrompt(): string
    {
        return 'You are a web developer. Generate a Node.js + Express.js project as JSON with these exact keys where each value is a STRING (not an object): src/app.ts, src/routes/index.ts, src/routes/api.ts, src/middleware/cors.ts, src/middleware/errorHandler.ts, src/types/index.ts, src/utils/logger.ts. Each value must be a complete file content as a string. DO NOT include configuration files like package.json, tsconfig.json, .env, etc. - these are handled by the system. Focus only on the application code and API routes. Return only valid JSON, no other text.';
    }

    /**
     * Generate Express.js specific user prompt
     */
    public function getUserPrompt(string $prompt): string
    {
        return "Create a Node.js + Express.js API server for: {$prompt}";
    }

    /**
     * Get required files for Express.js projects
     */
    public function getRequiredFiles(): array
    {
        return [
            'package.json',
            'tsconfig.json',
            '.env',
            'src/app.ts',
            'src/routes/index.ts',
            'src/routes/api.ts',
            'src/middleware/cors.ts',
            'src/middleware/errorHandler.ts',
            'src/types/index.ts',
            'src/utils/logger.ts',
            'Dockerfile',
            'docker-compose.yml',
            '.dockerignore',
        ];
    }

    /**
     * Generate mock Express.js project data
     */
    public function generateMockProject(string $prompt, string $projectType = 'api'): array
    {
        switch ($projectType) {
            case 'api':
                return $this->generateApiProject($prompt);
            case 'microservice':
                return $this->generateMicroserviceProject($prompt);
            case 'webhook':
                return $this->generateWebhookProject($prompt);
            case 'auth':
                return $this->generateAuthProject($prompt);
            case 'crud':
                return $this->generateCrudProject($prompt);
            default:
                return $this->generateGenericProject($prompt);
        }
    }

    /**
     * Generate Express.js API project
     */
    private function generateApiProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Express.js microservice project
     */
    private function generateMicroserviceProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Express.js webhook project
     */
    private function generateWebhookProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Express.js auth project
     */
    private function generateAuthProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Express.js CRUD project
     */
    private function generateCrudProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate generic Express.js project
     */
    private function generateGenericProject(string $prompt): array
    {
        return [
            'project_type' => 'nodejs-express',
            'name' => 'Express.js API',
            'description' => 'AI-generated Express.js application',
            'prompt' => $prompt,
            'files' => [
                [
                    'path' => 'src/app.ts',
                    'type' => 'file',
                    'content' => $this->getAppTsContent(),
                ],
                [
                    'path' => 'src/routes/index.ts',
                    'type' => 'file',
                    'content' => $this->getIndexRouteContent(),
                ],
                [
                    'path' => 'src/routes/api.ts',
                    'type' => 'file',
                    'content' => $this->getApiRouteContent(),
                ],
                [
                    'path' => 'src/middleware/cors.ts',
                    'type' => 'file',
                    'content' => $this->getCorsMiddlewareContent(),
                ],
                [
                    'path' => 'src/middleware/errorHandler.ts',
                    'type' => 'file',
                    'content' => $this->getErrorHandlerMiddlewareContent(),
                ],
                [
                    'path' => 'src/types/index.ts',
                    'type' => 'file',
                    'content' => $this->getTypesContent(),
                ],
                [
                    'path' => 'src/utils/logger.ts',
                    'type' => 'file',
                    'content' => $this->getLoggerContent(),
                ],
            ],
        ];
    }

    /**
     * Get Express.js app.ts content
     */
    private function getAppTsContent(): string
    {
        return 'import express from \'express\';
import cors from \'cors\';
import dotenv from \'dotenv\';
import { errorHandler } from \'./middleware/errorHandler\';
import { logger } from \'./utils/logger\';
import indexRoutes from \'./routes/index\';
import apiRoutes from \'./routes/api\';

// Load environment variables
dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Request logging
app.use((req, res, next) => {
  logger.info(`${req.method} ${req.path}`, {
    ip: req.ip,
    userAgent: req.get(\'User-Agent\')
  });
  next();
});

// Routes
app.use(\'/\', indexRoutes);
app.use(\'/api\', apiRoutes);

// Health check endpoint
app.get(\'/health\', (req, res) => {
  res.status(200).json({
    status: \'ok\',
    timestamp: new Date().toISOString(),
    uptime: process.uptime()
  });
});

// Error handling
app.use(errorHandler);

// 404 handler
app.use(\'*\', (req, res) => {
  res.status(404).json({
    error: \'Not Found\',
    message: `Route ${req.originalUrl} not found`
  });
});

// Start server
app.listen(PORT, \'0.0.0.0\', () => {
  logger.info(`Server running on port ${PORT}`);
  console.log(`🚀 Express server listening on http://localhost:${PORT}`);
});

export default app;';
    }

    /**
     * Get Express.js index routes content
     */
    private function getIndexRouteContent(): string
    {
        return 'import { Router } from \'express\';
import { logger } from \'../utils/logger\';

const router = Router();

// Welcome route
router.get(\'/\', (req, res) => {
  res.json({
    message: \'Welcome to Express.js API\',
    version: \'1.0.0\',
    timestamp: new Date().toISOString(),
    endpoints: {
      health: \'/health\',
      api: \'/api\',
      docs: \'/api/docs\'
    }
  });
});

// API info route
router.get(\'/info\', (req, res) => {
  res.json({
    name: \'Express.js API\',
    description: \'AI-generated Express.js application\',
    version: \'1.0.0\',
    environment: process.env.NODE_ENV || \'development\',
    nodeVersion: process.version
  });
});

export default router;';
    }

    /**
     * Get Express.js API routes content
     */
    private function getApiRouteContent(): string
    {
        return 'import { Router } from \'express\';
import { logger } from \'../utils/logger\';

const router = Router();

// API documentation
router.get(\'/docs\', (req, res) => {
  res.json({
    title: \'API Documentation\',
    version: \'1.0.0\',
    description: \'Express.js API endpoints\',
    endpoints: [
      {
        method: \'GET\',
        path: \'/api/users\',
        description: \'Get all users\'
      },
      {
        method: \'POST\',
        path: \'/api/users\',
        description: \'Create a new user\'
      },
      {
        method: \'GET\',
        path: \'/api/users/:id\',
        description: \'Get user by ID\'
      }
    ]
  });
});

// Users routes
router.get(\'/users\', (req, res) => {
  logger.info(\'Fetching all users\');
  res.json({
    message: \'Users endpoint\',
    data: [],
    count: 0
  });
});

router.post(\'/users\', (req, res) => {
  logger.info(\'Creating new user\', { body: req.body });
  res.status(201).json({
    message: \'User created successfully\',
    data: req.body
  });
});

router.get(\'/users/:id\', (req, res) => {
  const { id } = req.params;
  logger.info(`Fetching user with ID: ${id}`);
  res.json({
    message: \'User details\',
    data: { id, name: \'Sample User\', email: \'user@example.com\' }
  });
});

// Sample data endpoint
router.get(\'/data\', (req, res) => {
  res.json({
    message: \'Sample data endpoint\',
    data: {
      items: [
        { id: 1, name: \'Item 1\', value: 100 },
        { id: 2, name: \'Item 2\', value: 200 },
        { id: 3, name: \'Item 3\', value: 300 }
      ],
      total: 3
    }
  });
});

export default router;';
    }

    /**
     * Get CORS middleware content
     */
    private function getCorsMiddlewareContent(): string
    {
        return 'import cors from \'cors\';
import { Request } from \'express\';

const corsOptions = {
  origin: (origin: string | undefined, callback: (err: Error | null, allow?: boolean) => void) => {
    // Allow requests with no origin (mobile apps, curl, etc.)
    if (!origin) return callback(null, true);
    
    // Allow localhost and common development domains
    const allowedOrigins = [
      \'http://localhost:3000\',
      \'http://localhost:3001\',
      \'http://localhost:5173\',
      \'http://127.0.0.1:3000\',
      \'http://127.0.0.1:3001\',
      \'http://127.0.0.1:5173\'
    ];
    
    if (allowedOrigins.includes(origin)) {
      callback(null, true);
    } else {
      callback(new Error(\'Not allowed by CORS\'));
    }
  },
  credentials: true,
  methods: [\'GET\', \'POST\', \'PUT\', \'DELETE\', \'PATCH\', \'OPTIONS\'],
  allowedHeaders: [\'Content-Type\', \'Authorization\', \'X-Requested-With\']
};

export default cors(corsOptions);';
    }

    /**
     * Get error handler middleware content
     */
    private function getErrorHandlerMiddlewareContent(): string
    {
        return 'import { Request, Response, NextFunction } from \'express\';
import { logger } from \'../utils/logger\';

export interface AppError extends Error {
  statusCode?: number;
  isOperational?: boolean;
}

export const errorHandler = (
  err: AppError,
  req: Request,
  res: Response,
  next: NextFunction
): void => {
  let error = { ...err };
  error.message = err.message;

  // Log error
  logger.error(\'Error occurred\', {
    error: error.message,
    stack: error.stack,
    url: req.url,
    method: req.method,
    ip: req.ip
  });

  // Mongoose bad ObjectId
  if (err.name === \'CastError\') {
    const message = \'Resource not found\';
    error = { name: \'CastError\', message, statusCode: 404 };
  }

  // Mongoose duplicate key
  if (err.name === \'MongoError\' && (err as any).code === 11000) {
    const message = \'Duplicate field value entered\';
    error = { name: \'MongoError\', message, statusCode: 400 };
  }

  // Mongoose validation error
  if (err.name === \'ValidationError\') {
    const message = Object.values((err as any).errors).map((val: any) => val.message).join(\', \');
    error = { name: \'ValidationError\', message, statusCode: 400 };
  }

  res.status(error.statusCode || 500).json({
    success: false,
    error: error.message || \'Server Error\',
    ...(process.env.NODE_ENV === \'development\' && { stack: error.stack })
  });
};';
    }

    /**
     * Get types content
     */
    private function getTypesContent(): string
    {
        return '// Common types for the Express.js application

export interface User {
  id: string;
  name: string;
  email: string;
  createdAt: Date;
  updatedAt: Date;
}

export interface ApiResponse<T = any> {
  success: boolean;
  data?: T;
  message?: string;
  error?: string;
  count?: number;
}

export interface PaginationParams {
  page?: number;
  limit?: number;
  sort?: string;
  order?: \'asc\' | \'desc\';
}

export interface PaginatedResponse<T> extends ApiResponse<T[]> {
  pagination: {
    page: number;
    limit: number;
    total: number;
    pages: number;
  };
}

export interface ErrorResponse {
  success: false;
  error: string;
  message?: string;
  stack?: string;
}

// Request types
export interface CreateUserRequest {
  name: string;
  email: string;
}

export interface UpdateUserRequest {
  name?: string;
  email?: string;
}

// Environment variables
export interface EnvConfig {
  NODE_ENV: string;
  PORT: number;
  DATABASE_URL?: string;
  JWT_SECRET?: string;
  CORS_ORIGIN?: string;
}';
    }

    /**
     * Get logger utility content
     */
    private function getLoggerContent(): string
    {
        return 'import winston from \'winston\';

// Define log levels
const levels = {
  error: 0,
  warn: 1,
  info: 2,
  http: 3,
  debug: 4
};

// Define colors for each level
const colors = {
  error: \'red\',
  warn: \'yellow\',
  info: \'green\',
  http: \'magenta\',
  debug: \'white\'
};

// Tell winston that you want to link the colors
winston.addColors(colors);

// Define which transports the logger must use
const transports = [
  // Console transport
  new winston.transports.Console({
    format: winston.format.combine(
      winston.format.timestamp({ format: \'YYYY-MM-DD HH:mm:ss:ms\' }),
      winston.format.colorize({ all: true }),
      winston.format.printf(
        (info) => `${info.timestamp} ${info.level}: ${info.message}`
      )
    )
  }),
  // File transport for errors
  new winston.transports.File({
    filename: \'logs/error.log\',
    level: \'error\',
    format: winston.format.combine(
      winston.format.timestamp(),
      winston.format.json()
    )
  }),
  // File transport for all logs
  new winston.transports.File({
    filename: \'logs/combined.log\',
    format: winston.format.combine(
      winston.format.timestamp(),
      winston.format.json()
    )
  })
];

// Create the logger
export const logger = winston.createLogger({
  level: process.env.NODE_ENV === \'development\' ? \'debug\' : \'warn\',
  levels,
  transports,
  exitOnError: false
});

// Create a stream object with a \'write\' function that will be used by morgan
export const morganStream = {
  write: (message: string) => {
    logger.http(message.substring(0, message.lastIndexOf(\'\\n\')));
  }
};';
    }

    /**
     * Check if project is Express.js
     */
    public function isExpressProject(Project $project): bool
    {
        $settings = $project->settings ?? [];
        $stack = strtolower(trim($settings['stack'] ?? ''));

        return str_contains($stack, 'express') || str_contains($stack, 'nodejs');
    }

    /**
     * Get Express.js specific Docker configuration
     */
    public function getDockerConfig(): array
    {
        return [
            'port' => 3000,
            'build_command' => 'npm run build',
            'start_command' => 'npm start',
            'dev_command' => 'npm run dev',
        ];
    }

    /**
     * Create Express.js project files
     */
    public function createProjectFiles(string $projectDir, array $projectFiles): void
    {
        // Define protected files that should not be overwritten by AI
        $protectedFiles = [
            'package.json',
            'tsconfig.json',
            '.env',
            'Dockerfile',
            '.dockerignore',
            'docker-compose.yml',
        ];

        foreach ($projectFiles as $filePath => $content) {
            // Skip protected files - we'll create them ourselves
            if (in_array($filePath, $protectedFiles)) {
                Log::info('Skipping protected file from AI generation', [
                    'file' => $filePath,
                    'reason' => 'Protected configuration file',
                ]);

                continue;
            }

            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            // Create directory with proper permissions
            FilePermissionService::createDirectory($dir, 0755);

            // Create file with proper permissions
            FilePermissionService::createFile($fullPath, $content);
        }

        // BULLETPROOF PROTECTION: Delete any AI-generated config files that might have been written
        foreach ($protectedFiles as $protectedFile) {
            $protectedFilePath = "{$projectDir}/{$protectedFile}";
            if (file_exists($protectedFilePath)) {
                Log::warning('Deleting AI-generated protected file', [
                    'file' => $protectedFile,
                    'reason' => 'AI ignored protection instructions',
                ]);
                unlink($protectedFilePath);
            }
        }

        // Create additional configuration files (these will overwrite any AI attempts)
        $this->createConfigFiles($projectDir);

        // Create Dockerfile for Express.js project
        $this->createDockerfile($projectDir);
    }

    /**
     * Create additional Express.js configuration files
     */
    public function createConfigFiles(string $projectDir): void
    {
        // Create package.json
        $packageJsonPath = "{$projectDir}/package.json";
        if (! file_exists($packageJsonPath)) {
            $packageJson = <<<'JSON'
{
  "name": "express-api",
  "version": "1.0.0",
  "description": "AI-generated Express.js API server",
  "main": "dist/app.js",
  "scripts": {
    "dev": "nodemon src/app.ts",
    "build": "tsc",
    "start": "node dist/app.js",
    "test": "jest",
    "lint": "eslint src/**/*.ts",
    "lint:fix": "eslint src/**/*.ts --fix"
  },
  "keywords": ["express", "typescript", "api", "nodejs"],
  "author": "AI Generated",
  "license": "MIT",
  "dependencies": {
    "express": "^4.18.2",
    "cors": "^2.8.5",
    "dotenv": "^16.3.1",
    "winston": "^3.11.0",
    "helmet": "^7.1.0",
    "morgan": "^1.10.0"
  },
  "devDependencies": {
    "@types/express": "^4.17.21",
    "@types/cors": "^2.8.17",
    "@types/node": "^20.10.0",
    "@types/morgan": "^1.9.9",
    "@typescript-eslint/eslint-plugin": "^6.13.0",
    "@typescript-eslint/parser": "^6.13.0",
    "eslint": "^8.54.0",
    "jest": "^29.7.0",
    "nodemon": "^3.0.2",
    "ts-node": "^10.9.1",
    "typescript": "^5.3.2"
  }
}
JSON;
            file_put_contents($packageJsonPath, $packageJson);
        }

        // Create tsconfig.json
        $tsconfigPath = "{$projectDir}/tsconfig.json";
        if (! file_exists($tsconfigPath)) {
            $tsconfig = <<<'JSON'
{
  "compilerOptions": {
    "target": "ES2020",
    "module": "commonjs",
    "lib": ["ES2020"],
    "outDir": "./dist",
    "rootDir": "./src",
    "strict": true,
    "esModuleInterop": true,
    "skipLibCheck": true,
    "forceConsistentCasingInFileNames": true,
    "resolveJsonModule": true,
    "declaration": true,
    "declarationMap": true,
    "sourceMap": true,
    "removeComments": true,
    "noImplicitAny": true,
    "strictNullChecks": true,
    "strictFunctionTypes": true,
    "noImplicitThis": true,
    "noImplicitReturns": true,
    "noFallthroughCasesInSwitch": true,
    "moduleResolution": "node",
    "baseUrl": "./",
    "paths": {
      "@/*": ["src/*"]
    }
  },
  "include": ["src/**/*"],
  "exclude": ["node_modules", "dist", "**/*.test.ts"]
}
JSON;
            file_put_contents($tsconfigPath, $tsconfig);
        }

        // Create .env file
        $envPath = "{$projectDir}/.env";
        if (! file_exists($envPath)) {
            $envContent = <<<'ENV'
NODE_ENV=development
PORT=3000
HOST=0.0.0.0

# Database (uncomment and configure as needed)
# DATABASE_URL=mongodb://localhost:27017/express-api
# DATABASE_URL=postgresql://username:password@localhost:5432/express-api

# JWT Secret (generate a secure secret for production)
# JWT_SECRET=your-super-secret-jwt-key-here

# CORS Origin
CORS_ORIGIN=http://localhost:3000,http://localhost:3001,http://localhost:5173

# Logging
LOG_LEVEL=info
ENV;
            file_put_contents($envPath, $envContent);
        }

        // Create .eslintrc.json
        $eslintPath = "{$projectDir}/.eslintrc.json";
        if (! file_exists($eslintPath)) {
            $eslintConfig = <<<'JSON'
{
  "env": {
    "es2021": true,
    "node": true
  },
  "extends": [
    "eslint:recommended",
    "@typescript-eslint/recommended"
  ],
  "parser": "@typescript-eslint/parser",
  "parserOptions": {
    "ecmaVersion": "latest",
    "sourceType": "module"
  },
  "plugins": ["@typescript-eslint"],
  "rules": {
    "indent": ["error", 2],
    "linebreak-style": ["error", "unix"],
    "quotes": ["error", "single"],
    "semi": ["error", "always"],
    "@typescript-eslint/no-unused-vars": "error",
    "@typescript-eslint/explicit-function-return-type": "warn",
    "@typescript-eslint/no-explicit-any": "warn"
  }
}
JSON;
            file_put_contents($eslintPath, $eslintConfig);
        }

        // Create logs directory
        $logsDir = "{$projectDir}/logs";
        if (! is_dir($logsDir)) {
            mkdir($logsDir, 0755, true);
        }
    }

    /**
     * Create Dockerfile for Express.js projects (Development Mode for Live Previews)
     */
    public function createDockerfile(string $projectDir): void
    {
        $dockerfile = 'FROM node:18-alpine

WORKDIR /app

# Copy package files first for better caching
COPY package.json ./
COPY tsconfig.json ./

# Install dependencies
RUN npm install

# Copy source code
COPY . .

# Create logs directory
RUN mkdir -p logs

# Expose port
EXPOSE 3000

# Start the development server for live previews
CMD ["npm", "run", "dev"]';

        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Check if Express.js files already exist in the project directory
     */
    public function hasRequiredFiles(string $projectDir): bool
    {
        $requiredFiles = [
            'src/app.ts',
            'package.json',
            'tsconfig.json',
            '.env',
        ];

        foreach ($requiredFiles as $file) {
            if (! file_exists("{$projectDir}/{$file}")) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create basic Express.js fallback when no generated code is available
     */
    public function createBasicFallback(string $projectDir, Project $project): void
    {
        // Create a basic Express.js structure
        $basicExpress = [
            'package.json' => json_encode([
                'name' => strtolower($project->slug ?? 'express-api'),
                'version' => '1.0.0',
                'description' => 'AI-generated Express.js API server',
                'main' => 'dist/app.js',
                'scripts' => [
                    'dev' => 'nodemon src/app.ts',
                    'build' => 'tsc',
                    'start' => 'node dist/app.js',
                    'test' => 'jest',
                    'lint' => 'eslint src/**/*.ts',
                    'lint:fix' => 'eslint src/**/*.ts --fix',
                ],
                'keywords' => ['express', 'typescript', 'api', 'nodejs'],
                'author' => 'AI Generated',
                'license' => 'MIT',
                'dependencies' => [
                    'express' => '^4.18.2',
                    'cors' => '^2.8.5',
                    'dotenv' => '^16.3.1',
                    'winston' => '^3.11.0',
                    'helmet' => '^7.1.0',
                    'morgan' => '^1.10.0',
                ],
                'devDependencies' => [
                    '@types/express' => '^4.17.21',
                    '@types/cors' => '^2.8.17',
                    '@types/node' => '^20.10.0',
                    '@types/morgan' => '^1.9.9',
                    '@typescript-eslint/eslint-plugin' => '^6.13.0',
                    '@typescript-eslint/parser' => '^6.13.0',
                    'eslint' => '^8.54.0',
                    'jest' => '^29.7.0',
                    'nodemon' => '^3.0.2',
                    'ts-node' => '^10.9.1',
                    'typescript' => '^5.3.2',
                ],
            ], JSON_PRETTY_PRINT),
            'src/app.ts' => 'import express from \'express\';
import cors from \'cors\';
import dotenv from \'dotenv\';

// Load environment variables
dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Routes
app.get(\'/\', (req, res) => {
  res.json({
    message: \'Welcome to Express.js API\',
    version: \'1.0.0\',
    timestamp: new Date().toISOString()
  });
});

app.get(\'/health\', (req, res) => {
  res.status(200).json({
    status: \'ok\',
    timestamp: new Date().toISOString(),
    uptime: process.uptime()
  });
});

// Start server
app.listen(PORT, \'0.0.0.0\', () => {
  console.log(`🚀 Express server listening on http://localhost:${PORT}`);
});

export default app;',
            '.env' => 'NODE_ENV=development
PORT=3000
HOST=0.0.0.0

# Database (uncomment and configure as needed)
# DATABASE_URL=mongodb://localhost:27017/express-api

# JWT Secret (generate a secure secret for production)
# JWT_SECRET=your-super-secret-jwt-key-here

# CORS Origin
CORS_ORIGIN=http://localhost:3000,http://localhost:3001,http://localhost:5173

# Logging
LOG_LEVEL=info',
        ];

        // Create the files
        foreach ($basicExpress as $filePath => $content) {
            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $content);
        }

        // Create additional configuration files
        $this->createConfigFiles($projectDir);

        // Create Dockerfile for Express.js project
        $this->createDockerfile($projectDir);
    }

    /**
     * Get the internal port for Express.js projects
     */
    public function getInternalPort(): string
    {
        return '3000';
    }
}
