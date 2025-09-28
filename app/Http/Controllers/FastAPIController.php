<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DockerService;
use App\Services\FilePermissionService;
use Illuminate\Support\Facades\Log;

class FastAPIController extends Controller
{
    public function __construct(
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Generate FastAPI specific system prompt
     */
    public function getSystemPrompt(): string
    {
        return 'You are a web developer. Generate a Python + FastAPI project as JSON with these exact keys where each value is a STRING (not an object): main.py, app/api/routes.py, app/api/dependencies.py, app/core/config.py, app/core/security.py, app/models/schemas.py, app/services/database.py, app/utils/logger.py. Each value must be a complete file content as a string. DO NOT include configuration files like requirements.txt, pyproject.toml, Dockerfile, etc. - these are handled by the system. Focus only on the application code and API routes. Return only valid JSON, no other text.';
    }

    /**
     * Generate FastAPI specific user prompt
     */
    public function getUserPrompt(string $prompt): string
    {
        return "Create a Python + FastAPI API server with async support for: {$prompt}";
    }

    /**
     * Get required files for FastAPI projects
     */
    public function getRequiredFiles(): array
    {
        return [
            'requirements.txt',
            'pyproject.toml',
            '.env',
            'main.py',
            'app/__init__.py',
            'app/api/__init__.py',
            'app/api/routes.py',
            'app/api/dependencies.py',
            'app/core/__init__.py',
            'app/core/config.py',
            'app/core/security.py',
            'app/models/__init__.py',
            'app/models/schemas.py',
            'app/services/__init__.py',
            'app/services/database.py',
            'app/utils/__init__.py',
            'app/utils/logger.py',
            'Dockerfile',
            'docker-compose.yml',
            '.dockerignore',
        ];
    }

    /**
     * Generate mock FastAPI project data
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
     * Generate FastAPI API project
     */
    private function generateApiProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate FastAPI microservice project
     */
    private function generateMicroserviceProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate FastAPI webhook project
     */
    private function generateWebhookProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate FastAPI auth project
     */
    private function generateAuthProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate FastAPI CRUD project
     */
    private function generateCrudProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate generic FastAPI project
     */
    private function generateGenericProject(string $prompt): array
    {
        return [
            'project_type' => 'python-fastapi',
            'name' => 'FastAPI Application',
            'description' => 'AI-generated FastAPI application',
            'prompt' => $prompt,
            'files' => [
                [
                    'path' => 'main.py',
                    'type' => 'file',
                    'content' => $this->getMainPyContent(),
                ],
                [
                    'path' => 'app/api/routes.py',
                    'type' => 'file',
                    'content' => $this->getRoutesPyContent(),
                ],
                [
                    'path' => 'app/api/dependencies.py',
                    'type' => 'file',
                    'content' => $this->getDependenciesPyContent(),
                ],
                [
                    'path' => 'app/core/config.py',
                    'type' => 'file',
                    'content' => $this->getConfigPyContent(),
                ],
                [
                    'path' => 'app/core/security.py',
                    'type' => 'file',
                    'content' => $this->getSecurityPyContent(),
                ],
                [
                    'path' => 'app/models/schemas.py',
                    'type' => 'file',
                    'content' => $this->getSchemasPyContent(),
                ],
                [
                    'path' => 'app/services/database.py',
                    'type' => 'file',
                    'content' => $this->getDatabasePyContent(),
                ],
                [
                    'path' => 'app/utils/logger.py',
                    'type' => 'file',
                    'content' => $this->getLoggerPyContent(),
                ],
            ],
        ];
    }

    /**
     * Get FastAPI main.py content
     */
    private function getMainPyContent(): string
    {
        return 'from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
from fastapi.middleware.trustedhost import TrustedHostMiddleware
from fastapi.responses import JSONResponse
import uvicorn
import logging
from contextlib import asynccontextmanager

from app.core.config import settings
from app.api.routes import api_router
from app.utils.logger import setup_logging

# Setup logging
setup_logging()
logger = logging.getLogger(__name__)

@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup
    logger.info("Starting up FastAPI application...")
    yield
    # Shutdown
    logger.info("Shutting down FastAPI application...")

# Create FastAPI app
app = FastAPI(
    title=settings.PROJECT_NAME,
    description="AI-generated FastAPI application with async support",
    version="1.0.0",
    openapi_url=f"{settings.API_V1_STR}/openapi.json",
    lifespan=lifespan
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.BACKEND_CORS_ORIGINS,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Add trusted host middleware
app.add_middleware(
    TrustedHostMiddleware,
    allowed_hosts=settings.ALLOWED_HOSTS
)

# Include API router
app.include_router(api_router, prefix=settings.API_V1_STR)

@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "message": "Welcome to FastAPI",
        "version": "1.0.0",
        "docs": "/docs",
        "redoc": "/redoc"
    }

@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "message": "FastAPI application is running"
    }

@app.exception_handler(Exception)
async def global_exception_handler(request: Request, exc: Exception):
    """Global exception handler"""
    logger.error(f"Global exception: {exc}", exc_info=True)
    return JSONResponse(
        status_code=500,
        content={
            "detail": "Internal server error",
            "message": "An unexpected error occurred"
        }
    )

if __name__ == "__main__":
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8000,
        reload=True,
        log_level="info"
    )';
    }

    /**
     * Get FastAPI routes content
     */
    private function getRoutesPyContent(): string
    {
        return 'from fastapi import APIRouter, Depends, HTTPException, status
from typing import List, Optional
from sqlalchemy.orm import Session

from app.api.dependencies import get_db
from app.models.schemas import (
    UserCreate,
    UserResponse,
    UserUpdate,
    ItemCreate,
    ItemResponse,
    ItemUpdate
)
from app.services.database import get_user, get_users, create_user, update_user, delete_user

router = APIRouter()

@router.get("/")
async def read_root():
    """Root API endpoint"""
    return {
        "message": "FastAPI API is running",
        "version": "1.0.0",
        "endpoints": {
            "users": "/api/v1/users",
            "items": "/api/v1/items",
            "docs": "/docs"
        }
    }

@router.get("/users", response_model=List[UserResponse])
async def read_users(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db)
):
    """Get all users"""
    users = get_users(db, skip=skip, limit=limit)
    return users

@router.post("/users", response_model=UserResponse, status_code=status.HTTP_201_CREATED)
async def create_user_endpoint(
    user: UserCreate,
    db: Session = Depends(get_db)
):
    """Create a new user"""
    return create_user(db=db, user=user)

@router.get("/users/{user_id}", response_model=UserResponse)
async def read_user(
    user_id: int,
    db: Session = Depends(get_db)
):
    """Get user by ID"""
    user = get_user(db, user_id=user_id)
    if user is None:
        raise HTTPException(status_code=404, detail="User not found")
    return user

@router.put("/users/{user_id}", response_model=UserResponse)
async def update_user_endpoint(
    user_id: int,
    user_update: UserUpdate,
    db: Session = Depends(get_db)
):
    """Update user by ID"""
    user = update_user(db=db, user_id=user_id, user_update=user_update)
    if user is None:
        raise HTTPException(status_code=404, detail="User not found")
    return user

@router.delete("/users/{user_id}")
async def delete_user_endpoint(
    user_id: int,
    db: Session = Depends(get_db)
):
    """Delete user by ID"""
    success = delete_user(db=db, user_id=user_id)
    if not success:
        raise HTTPException(status_code=404, detail="User not found")
    return {"message": "User deleted successfully"}

@router.get("/items", response_model=List[ItemResponse])
async def read_items(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db)
):
    """Get all items"""
    # This would be implemented with actual database queries
    return []

@router.post("/items", response_model=ItemResponse, status_code=status.HTTP_201_CREATED)
async def create_item(
    item: ItemCreate,
    db: Session = Depends(get_db)
):
    """Create a new item"""
    # This would be implemented with actual database operations
    return item';
    }

    /**
     * Get FastAPI dependencies content
     */
    private function getDependenciesPyContent(): string
    {
        return 'from fastapi import Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from sqlalchemy.orm import Session
from typing import Generator

from app.core.config import settings
from app.core.security import verify_token
from app.services.database import get_db

# Security scheme
security = HTTPBearer()

async def get_current_user(
    credentials: HTTPAuthorizationCredentials = Depends(security),
    db: Session = Depends(get_db)
):
    """Get current authenticated user"""
    token = credentials.credentials
    payload = verify_token(token)
    if payload is None:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Could not validate credentials",
            headers={"WWW-Authenticate": "Bearer"},
        )
    return payload

async def get_current_active_user(
    current_user: dict = Depends(get_current_user)
):
    """Get current active user"""
    if not current_user.get("is_active", True):
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Inactive user"
        )
    return current_user

def get_db() -> Generator:
    """Get database session"""
    db = next(get_db())
    try:
        yield db
    finally:
        db.close()';
    }

    /**
     * Get FastAPI config content
     */
    private function getConfigPyContent(): string
    {
        return 'from pydantic_settings import BaseSettings
from typing import List, Optional
import os

class Settings(BaseSettings):
    PROJECT_NAME: str = "FastAPI Application"
    API_V1_STR: str = "/api/v1"
    
    # Database
    DATABASE_URL: Optional[str] = None
    
    # Security
    SECRET_KEY: str = "your-secret-key-here"
    ACCESS_TOKEN_EXPIRE_MINUTES: int = 30
    
    # CORS
    BACKEND_CORS_ORIGINS: List[str] = [
        "http://localhost:3000",
        "http://localhost:3001",
        "http://localhost:5173",
        "http://127.0.0.1:3000",
        "http://127.0.0.1:3001",
        "http://127.0.0.1:5173"
    ]
    
    # Allowed hosts
    ALLOWED_HOSTS: List[str] = ["*"]
    
    # Logging
    LOG_LEVEL: str = "INFO"
    
    class Config:
        env_file = ".env"
        case_sensitive = True

# Create settings instance
settings = Settings()';
    }

    /**
     * Get FastAPI security content
     */
    private function getSecurityPyContent(): string
    {
        return 'from datetime import datetime, timedelta
from typing import Optional, Union
from jose import JWTError, jwt
from passlib.context import CryptContext
from fastapi import HTTPException, status

from app.core.config import settings

# Password hashing
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

def verify_password(plain_password: str, hashed_password: str) -> bool:
    """Verify a password against its hash"""
    return pwd_context.verify(plain_password, hashed_password)

def get_password_hash(password: str) -> str:
    """Hash a password"""
    return pwd_context.hash(password)

def create_access_token(data: dict, expires_delta: Optional[timedelta] = None) -> str:
    """Create access token"""
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=settings.ACCESS_TOKEN_EXPIRE_MINUTES)
    
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, settings.SECRET_KEY, algorithm="HS256")
    return encoded_jwt

def verify_token(token: str) -> Optional[dict]:
    """Verify and decode token"""
    try:
        payload = jwt.decode(token, settings.SECRET_KEY, algorithms=["HS256"])
        return payload
    except JWTError:
        return None

def authenticate_user(username: str, password: str) -> Optional[dict]:
    """Authenticate user with username and password"""
    # This would typically check against a database
    # For demo purposes, we\'ll use a simple check
    if username == "admin" and password == "admin":
        return {
            "username": username,
            "is_active": True,
            "is_admin": True
        }
    return None';
    }

    /**
     * Get FastAPI schemas content
     */
    private function getSchemasPyContent(): string
    {
        return 'from pydantic import BaseModel, EmailStr
from typing import Optional, List
from datetime import datetime

# Base schemas
class UserBase(BaseModel):
    email: EmailStr
    username: str
    full_name: Optional[str] = None
    is_active: bool = True

class UserCreate(UserBase):
    password: str

class UserUpdate(BaseModel):
    email: Optional[EmailStr] = None
    username: Optional[str] = None
    full_name: Optional[str] = None
    is_active: Optional[bool] = None
    password: Optional[str] = None

class UserResponse(UserBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True

# Item schemas
class ItemBase(BaseModel):
    title: str
    description: Optional[str] = None
    price: Optional[float] = None
    is_available: bool = True

class ItemCreate(ItemBase):
    pass

class ItemUpdate(BaseModel):
    title: Optional[str] = None
    description: Optional[str] = None
    price: Optional[float] = None
    is_available: Optional[bool] = None

class ItemResponse(ItemBase):
    id: int
    created_at: datetime
    updated_at: datetime

    class Config:
        from_attributes = True

# Token schemas
class Token(BaseModel):
    access_token: str
    token_type: str

class TokenData(BaseModel):
    username: Optional[str] = None

# API Response schemas
class Message(BaseModel):
    message: str

class ErrorResponse(BaseModel):
    detail: str
    message: Optional[str] = None';
    }

    /**
     * Get FastAPI database content
     */
    private function getDatabasePyContent(): string
    {
        return 'from sqlalchemy import create_engine, Column, Integer, String, Boolean, DateTime, Float, Text
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, Session
from datetime import datetime
from typing import List, Optional

from app.core.config import settings
from app.models.schemas import UserCreate, UserUpdate, ItemCreate, ItemUpdate

# Database setup
SQLALCHEMY_DATABASE_URL = settings.DATABASE_URL or "sqlite:///./app.db"

engine = create_engine(
    SQLALCHEMY_DATABASE_URL,
    connect_args={"check_same_thread": False} if "sqlite" in SQLALCHEMY_DATABASE_URL else {}
)

SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

# Database models
class User(Base):
    __tablename__ = "users"
    
    id = Column(Integer, primary_key=True, index=True)
    username = Column(String, unique=True, index=True, nullable=False)
    email = Column(String, unique=True, index=True, nullable=False)
    full_name = Column(String, nullable=True)
    hashed_password = Column(String, nullable=False)
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

class Item(Base):
    __tablename__ = "items"
    
    id = Column(Integer, primary_key=True, index=True)
    title = Column(String, index=True, nullable=False)
    description = Column(Text, nullable=True)
    price = Column(Float, nullable=True)
    is_available = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

# Database dependency
def get_db() -> Session:
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# User CRUD operations
def get_user(db: Session, user_id: int) -> Optional[User]:
    return db.query(User).filter(User.id == user_id).first()

def get_user_by_email(db: Session, email: str) -> Optional[User]:
    return db.query(User).filter(User.email == email).first()

def get_user_by_username(db: Session, username: str) -> Optional[User]:
    return db.query(User).filter(User.username == username).first()

def get_users(db: Session, skip: int = 0, limit: int = 100) -> List[User]:
    return db.query(User).offset(skip).limit(limit).all()

def create_user(db: Session, user: UserCreate) -> User:
    from app.core.security import get_password_hash
    
    hashed_password = get_password_hash(user.password)
    db_user = User(
        username=user.username,
        email=user.email,
        full_name=user.full_name,
        hashed_password=hashed_password,
        is_active=user.is_active
    )
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    return db_user

def update_user(db: Session, user_id: int, user_update: UserUpdate) -> Optional[User]:
    db_user = get_user(db, user_id)
    if not db_user:
        return None
    
    update_data = user_update.dict(exclude_unset=True)
    if "password" in update_data:
        from app.core.security import get_password_hash
        update_data["hashed_password"] = get_password_hash(update_data.pop("password"))
    
    for field, value in update_data.items():
        setattr(db_user, field, value)
    
    db_user.updated_at = datetime.utcnow()
    db.commit()
    db.refresh(db_user)
    return db_user

def delete_user(db: Session, user_id: int) -> bool:
    db_user = get_user(db, user_id)
    if not db_user:
        return False
    
    db.delete(db_user)
    db.commit()
    return True';
    }

    /**
     * Get FastAPI logger content
     */
    private function getLoggerPyContent(): string
    {
        return 'import logging
import sys
from typing import Any, Dict, Optional
from datetime import datetime

def setup_logging() -> None:
    """Setup logging configuration"""
    logging.basicConfig(
        level=logging.INFO,
        format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
        handlers=[
            logging.StreamHandler(sys.stdout),
            logging.FileHandler("app.log")
        ]
    )

def get_logger(name: str) -> logging.Logger:
    """Get logger instance"""
    return logging.getLogger(name)

class Logger:
    """Custom logger class"""
    
    def __init__(self, name: str):
        self.logger = get_logger(name)
    
    def info(self, message: str, extra: Optional[Dict[str, Any]] = None) -> None:
        """Log info message"""
        if extra:
            self.logger.info(f"{message} - {extra}")
        else:
            self.logger.info(message)
    
    def error(self, message: str, extra: Optional[Dict[str, Any]] = None) -> None:
        """Log error message"""
        if extra:
            self.logger.error(f"{message} - {extra}")
        else:
            self.logger.error(message)
    
    def warning(self, message: str, extra: Optional[Dict[str, Any]] = None) -> None:
        """Log warning message"""
        if extra:
            self.logger.warning(f"{message} - {extra}")
        else:
            self.logger.warning(message)
    
    def debug(self, message: str, extra: Optional[Dict[str, Any]] = None) -> None:
        """Log debug message"""
        if extra:
            self.logger.debug(f"{message} - {extra}")
        else:
            self.logger.debug(message)';
    }

    /**
     * Check if project is FastAPI
     */
    public function isFastAPIProject(Project $project): bool
    {
        $settings = $project->settings ?? [];
        $stack = strtolower(trim($settings['stack'] ?? ''));

        return str_contains($stack, 'fastapi') || str_contains($stack, 'python');
    }

    /**
     * Get FastAPI specific Docker configuration
     */
    public function getDockerConfig(): array
    {
        return [
            'port' => 8000,
            'build_command' => 'pip install -r requirements.txt',
            'start_command' => 'uvicorn main:app --host 0.0.0.0 --port 8000',
            'dev_command' => 'uvicorn main:app --host 0.0.0.0 --port 8000 --reload',
        ];
    }

    /**
     * Create FastAPI project files
     */
    public function createProjectFiles(string $projectDir, array $projectFiles): void
    {
        // Define protected files that should not be overwritten by AI
        $protectedFiles = [
            'requirements.txt',
            'pyproject.toml',
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

        // Create Dockerfile for FastAPI project
        $this->createDockerfile($projectDir);
    }

    /**
     * Create additional FastAPI configuration files
     */
    public function createConfigFiles(string $projectDir): void
    {
        // Create requirements.txt
        $requirementsPath = "{$projectDir}/requirements.txt";
        if (! file_exists($requirementsPath)) {
            $requirements = <<<'TXT'
fastapi==0.104.1
uvicorn[standard]==0.24.0
pydantic==2.5.0
pydantic-settings==2.1.0
sqlalchemy==2.0.23
alembic==1.13.1
python-jose[cryptography]==3.3.0
passlib[bcrypt]==1.7.4
python-multipart==0.0.6
email-validator==2.1.0
python-dotenv==1.0.0
httpx==0.25.2
pytest==7.4.3
pytest-asyncio==0.21.1
pytest-cov==4.1.0
black==23.11.0
isort==5.12.0
flake8==6.1.0
mypy==1.7.1
TXT;
            file_put_contents($requirementsPath, $requirements);
        }

        // Create pyproject.toml
        $pyprojectPath = "{$projectDir}/pyproject.toml";
        if (! file_exists($pyprojectPath)) {
            $pyproject = <<<'TOML'
[build-system]
requires = ["setuptools>=45", "wheel"]
build-backend = "setuptools.build_meta"

[project]
name = "fastapi-app"
version = "1.0.0"
description = "AI-generated FastAPI application"
authors = [{name = "AI Generated", email = "ai@example.com"}]
license = {text = "MIT"}
readme = "README.md"
requires-python = ">=3.8"
classifiers = [
    "Development Status :: 4 - Beta",
    "Intended Audience :: Developers",
    "License :: OSI Approved :: MIT License",
    "Programming Language :: Python :: 3",
    "Programming Language :: Python :: 3.8",
    "Programming Language :: Python :: 3.9",
    "Programming Language :: Python :: 3.10",
    "Programming Language :: Python :: 3.11",
]

[project.optional-dependencies]
dev = [
    "pytest>=7.4.3",
    "pytest-asyncio>=0.21.1",
    "pytest-cov>=4.1.0",
    "black>=23.11.0",
    "isort>=5.12.0",
    "flake8>=6.1.0",
    "mypy>=1.7.1",
]

[tool.black]
line-length = 88
target-version = ['py38']

[tool.isort]
profile = "black"
multi_line_output = 3

[tool.mypy]
python_version = "3.8"
warn_return_any = true
warn_unused_configs = true
disallow_untyped_defs = true
TOML;
            file_put_contents($pyprojectPath, $pyproject);
        }

        // Create .env file
        $envPath = "{$projectDir}/.env";
        if (! file_exists($envPath)) {
            $envContent = <<<'ENV'
# FastAPI Configuration
PROJECT_NAME=FastAPI Application
API_V1_STR=/api/v1

# Database
DATABASE_URL=sqlite:///./app.db
# DATABASE_URL=postgresql://username:password@localhost:5432/fastapi_app
# DATABASE_URL=mysql://username:password@localhost:3306/fastapi_app

# Security
SECRET_KEY=your-super-secret-key-change-this-in-production
ACCESS_TOKEN_EXPIRE_MINUTES=30

# CORS
BACKEND_CORS_ORIGINS=["http://localhost:3000","http://localhost:3001","http://localhost:5173"]

# Allowed Hosts
ALLOWED_HOSTS=["*"]

# Logging
LOG_LEVEL=INFO
ENV;
            file_put_contents($envPath, $envContent);
        }

        // Create .gitignore
        $gitignorePath = "{$projectDir}/.gitignore";
        if (! file_exists($gitignorePath)) {
            $gitignore = <<<'GITIGNORE'
# Python
__pycache__/
*.py[cod]
*$py.class
*.so
.Python
build/
develop-eggs/
dist/
downloads/
eggs/
.eggs/
lib/
lib64/
parts/
sdist/
var/
wheels/
*.egg-info/
.installed.cfg
*.egg

# Virtual Environment
venv/
env/
ENV/
env.bak/
venv.bak/

# IDE
.vscode/
.idea/
*.swp
*.swo

# OS
.DS_Store
Thumbs.db

# Logs
*.log
logs/

# Database
*.db
*.sqlite
*.sqlite3

# Environment
.env
.env.local
.env.production

# Coverage
htmlcov/
.coverage
.coverage.*
coverage.xml
*.cover
.hypothesis/
.pytest_cache/

# mypy
.mypy_cache/
.dmypy.json
dmypy.json
GITIGNORE;
            file_put_contents($gitignorePath, $gitignore);
        }

        // Create __init__.py files for Python packages
        $initFiles = [
            'app/__init__.py',
            'app/api/__init__.py',
            'app/core/__init__.py',
            'app/models/__init__.py',
            'app/services/__init__.py',
            'app/utils/__init__.py',
        ];

        foreach ($initFiles as $initFile) {
            $initPath = "{$projectDir}/{$initFile}";
            $initDir = dirname($initPath);
            
            // Create directory if it doesn't exist
            if (! is_dir($initDir)) {
                FilePermissionService::createDirectory($initDir, 0755);
            }
            
            if (! file_exists($initPath)) {
                file_put_contents($initPath, '');
            }
        }
    }

    /**
     * Create Dockerfile for FastAPI projects (Development Mode for Live Previews)
     */
    public function createDockerfile(string $projectDir): void
    {
        $dockerfile = 'FROM python:3.11-slim

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \\
    gcc \\
    && rm -rf /var/lib/apt/lists/*

# Copy requirements first for better caching
COPY requirements.txt .

# Install Python dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Copy source code
COPY . .

# Create logs directory
RUN mkdir -p logs

# Expose port
EXPOSE 8000

# Start the development server for live previews
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000", "--reload"]';

        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Check if FastAPI files already exist in the project directory
     */
    public function hasRequiredFiles(string $projectDir): bool
    {
        $requiredFiles = [
            'main.py',
            'requirements.txt',
            'pyproject.toml',
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
     * Create basic FastAPI fallback when no generated code is available
     */
    public function createBasicFallback(string $projectDir, Project $project): void
    {
        // Create a basic FastAPI structure
        $basicFastAPI = [
            'main.py' => 'from fastapi import FastAPI
import uvicorn

app = FastAPI(
    title="FastAPI Application",
    description="AI-generated FastAPI application with async support",
    version="1.0.0"
)

@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "message": "Welcome to FastAPI",
        "version": "1.0.0",
        "docs": "/docs"
    }

@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "message": "FastAPI application is running"
    }

if __name__ == "__main__":
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8000,
        reload=True
    )',
            'requirements.txt' => 'fastapi==0.104.1
uvicorn[standard]==0.24.0
pydantic==2.5.0
pydantic-settings==2.1.0
python-dotenv==1.0.0',
            '.env' => 'PROJECT_NAME=FastAPI Application
API_V1_STR=/api/v1
SECRET_KEY=your-super-secret-key-change-this-in-production
ACCESS_TOKEN_EXPIRE_MINUTES=30
LOG_LEVEL=INFO',
        ];

        // Create the files
        foreach ($basicFastAPI as $filePath => $content) {
            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $content);
        }

        // Create additional configuration files
        $this->createConfigFiles($projectDir);

        // Create Dockerfile for FastAPI project
        $this->createDockerfile($projectDir);
    }

    /**
     * Get the internal port for FastAPI projects
     */
    public function getInternalPort(): string
    {
        return '8000';
    }
}
