# 🚀 TaskFlow - Task Management System

> A comprehensive RESTful API for task management built with Laravel 12, featuring authentication, CRUD operations, advanced filtering, and production-ready features.

[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Database Schema](#database-schema)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [Bonus Features](#bonus-features)
- [Project Structure](#project-structure)
- [Design Decisions](#design-decisions)
- [Security](#security)
- [Contributing](#contributing)

---

## 🎯 Overview

TaskFlow is a modern task management API developed as part of a technical assignment. It demonstrates proficiency in Laravel development, RESTful API design, database management, testing, and adherence to industry best practices.

**Assignment Duration:** 2 Days  
**Completion Status:** 100% Core Features + Bonus Features  
**Test Coverage:** Comprehensive feature tests included

---

## ✨ Features

### Core Features ✅

- **User Authentication**
  - User registration with validation
  - Secure login/logout using Laravel Sanctum
  - Token-based API authentication

- **Task Management**
  - Create, read, update, delete tasks
  - Soft delete with restore capability
  - Rich task attributes (title, description, priority, status, due date)
  - Multiple categories per task

- **Category System**
  - Create and manage categories
  - Color-coded categories with hex color support
  - Many-to-many relationship with tasks

- **Advanced Filtering & Search**
  - Filter by status (pending, in_progress, completed)
  - Filter by priority (low, medium, high)
  - Filter by category
  - Full-text search in title and description
  - Sort by multiple fields (created_at, due_date, priority)
  - Pagination support

- **Task Statistics**
  - Total task count
  - Count by status and priority
  - Overdue tasks tracking
  - Tasks due this week

- **Form Validation**
  - Custom Form Request classes
  - Detailed validation rules
  - Custom error messages

- **API Resources**
  - Formatted JSON responses
  - Consistent API structure
  - Proper HTTP status codes

### Bonus Features Implemented 🎁

- **API Rate Limiting** ⭐
  - Prevents API abuse
  - Configured limits: 60 requests/minute for general endpoints
  - Login endpoint: 6 requests/minute
  - User-based rate limiting

- **Task Observer** ⭐
  - Automatic audit logging
  - Logs task creation, updates, and deletion
  - Tracks user actions for debugging
  - Improves accountability and traceability

---

## 🛠️ Tech Stack

| Technology | Purpose |
|------------|---------|
| Laravel 12 | Backend Framework |
| MySQL | Primary Database |
| Laravel Sanctum | API Authentication |
| PHPUnit | Testing Framework |
| Eloquent ORM | Database Interactions |
| Git | Version Control |

---

## 🚀 Installation

### Prerequisites

Ensure you have the following installed:
- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Git

### Step-by-Step Setup

#### 1. Clone the Repository
```bash
git clone https://github.com/YOUR_USERNAME/TaskFlow.git
cd TaskFlow
```

#### 2. Install Dependencies
```bash
composer install
```

#### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Configure Database
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 5. Create Database
```bash
# Open MySQL console and run:
CREATE DATABASE taskflow_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 6. Run Migrations & Seeders
```bash
php artisan migrate --seed
```

This will create all tables and populate them with sample data:
- 3 demo users
- 7 categories (Work, Personal, Urgent, Shopping, Health, Finance, Education)
- 18 sample tasks with relationships

#### 7. Start Development Server
```bash
php artisan serve
```

API is now available at: **http://localhost:8000**

---

## 📦 Database Schema

### Entity Relationship Diagram

```
Users (1) ──── (N) Tasks (N) ──── (N) Categories
                      │
                      └── Soft Deletes
```

### Tables Structure

#### users
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | User's full name |
| email | varchar(255) | Unique email |
| password | varchar(255) | Hashed password |
| created_at | timestamp | Account creation |
| updated_at | timestamp | Last update |

#### tasks
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users |
| title | varchar(255) | Task title (required) |
| description | text | Task details (nullable) |
| priority | enum | low, medium, high |
| status | enum | pending, in_progress, completed |
| due_date | date | Deadline (nullable) |
| created_at | timestamp | Task creation |
| updated_at | timestamp | Last update |
| deleted_at | timestamp | Soft delete (nullable) |

**Indexes:**
- `user_id, status` (composite) - For efficient filtering
- `due_date` - For date-based queries
- `priority` - For priority sorting

#### categories
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | Unique category name |
| color | varchar(7) | Hex color code (#RRGGBB) |
| created_at | timestamp | Category creation |
| updated_at | timestamp | Last update |

#### category_task (Pivot Table)
| Column | Type | Description |
|--------|------|-------------|
| task_id | bigint | Foreign key to tasks |
| category_id | bigint | Foreign key to categories |

**Composite Primary Key:** (task_id, category_id)

---

## 📡 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication

All endpoints (except register/login) require a Bearer token in the Authorization header:
```
Authorization: Bearer {your_token_here}
```

---

### 🔐 Authentication Endpoints

#### 1. Register New User

**Endpoint:** `POST /api/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Validation Rules:**
- name: required, max 255 characters
- email: required, valid email, unique
- password: required, min 8 characters, confirmed

**Response (201 Created):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-12-19 10:00:00"
  },
  "access_token": "1|xyz...",
  "token_type": "Bearer"
}
```

#### 2. Login

**Endpoint:** `POST /api/login`

**Rate Limit:** 6 requests/minute

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200 OK):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-12-19 10:00:00"
  },
  "access_token": "2|abc...",
  "token_type": "Bearer"
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

#### 3. Logout

**Endpoint:** `POST /api/logout`

**Headers:** `Authorization: Bearer {token}`

**Response (200 OK):**
```json
{
  "message": "Logged out successfully"
}
```

#### 4. Get Current User

**Endpoint:** `GET /api/user`

**Headers:** `Authorization: Bearer {token}`

**Response (200 OK):**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "created_at": "2025-12-19 10:00:00"
}
```

---

### 📝 Task Endpoints

#### 1. Get All Tasks (with Filtering)

**Endpoint:** `GET /api/tasks`

**Rate Limit:** 60 requests/minute

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| status | string | Filter by status | `pending`, `in_progress`, `completed` |
| priority | string | Filter by priority | `low`, `medium`, `high` |
| category | integer | Filter by category ID | `1` |
| search | string | Search in title/description | `Laravel` |
| sort_by | string | Sort field | `created_at`, `due_date`, `priority` |
| sort_order | string | Sort direction | `asc`, `desc` |
| per_page | integer | Items per page | `10`, `15`, `20` |

**Example Request:**
```
GET /api/tasks?status=pending&priority=high&search=Laravel&sort_by=due_date&sort_order=asc&per_page=10
```

**Response (200 OK):**
```json
{
  "data": {
    "data": [
      {
        "id": 1,
        "title": "Complete Laravel Assignment",
        "description": "Build task management system with Laravel 12",
        "priority": "high",
        "status": "in_progress",
        "due_date": "2025-12-25",
        "is_overdue": false,
        "categories": [
          {
            "id": 1,
            "name": "Work",
            "color": "#3B82F6",
            "created_at": "2025-12-19 10:00:00"
          }
        ],
        "created_at": "2025-12-19 10:00:00",
        "updated_at": "2025-12-19 10:00:00"
      }
    ],
    "meta": {
      "total": 18,
      "count": 1,
      "per_page": 10,
      "current_page": 1,
      "total_pages": 2
    },
    "links": {
      "first": "http://localhost:8000/api/tasks?page=1",
      "last": "http://localhost:8000/api/tasks?page=2",
      "prev": null,
      "next": "http://localhost:8000/api/tasks?page=2"
    }
  },
  "status": "success",
  "version": "1.0.0"
}
```

#### 2. Create New Task

**Endpoint:** `POST /api/tasks`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "title": "New Task",
  "description": "Task description here",
  "priority": "high",
  "status": "pending",
  "due_date": "2025-12-25",
  "categories": [1, 2, 3]
}
```

**Validation Rules:**
- title: required, max 255 characters
- description: optional, max 5000 characters
- priority: required, must be `low`, `medium`, or `high`
- status: required, must be `pending`, `in_progress`, or `completed`
- due_date: optional, must be future date
- categories: optional array of existing category IDs

**Response (201 Created):**
```json
{
  "data": {
    "id": 19,
    "title": "New Task",
    "description": "Task description here",
    "priority": "high",
    "status": "pending",
    "due_date": "2025-12-25",
    "is_overdue": false,
    "categories": [
      {
        "id": 1,
        "name": "Work",
        "color": "#3B82F6",
        "created_at": "2025-12-19 10:00:00"
      }
    ],
    "created_at": "2025-12-19 11:30:00",
    "updated_at": "2025-12-19 11:30:00"
  },
  "meta": {
    "version": "1.0.0"
  }
}
```

#### 3. Get Single Task

**Endpoint:** `GET /api/tasks/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200 OK):** Same structure as Create Task response

**Error Response (404 Not Found):**
```json
{
  "message": "Task not found"
}
```

#### 4. Update Task

**Endpoint:** `PUT /api/tasks/{id}`

**Headers:** `Authorization: Bearer {token}`

**Request Body:** (All fields optional)
```json
{
  "title": "Updated Task Title",
  "status": "completed",
  "categories": [2, 3]
}
```

**Response (200 OK):** Updated task object

#### 5. Delete Task (Soft Delete)

**Endpoint:** `DELETE /api/tasks/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200 OK):**
```json
{
  "message": "Task deleted successfully"
}
```

**Note:** Task is soft deleted, not permanently removed. Can be restored later.

#### 6. Get Task Statistics

**Endpoint:** `GET /api/tasks/statistics`

**Headers:** `Authorization: Bearer {token}`

**Response (200 OK):**
```json
{
  "data": {
    "total_tasks": 18,
    "by_status": {
      "pending": 6,
      "in_progress": 8,
      "completed": 4
    },
    "by_priority": {
      "low": 5,
      "medium": 8,
      "high": 5
    },
    "overdue_tasks": 2,
    "due_this_week": 5
  }
}
```

#### 7. Restore Deleted Task

**Endpoint:** `POST /api/tasks/{id}/restore`

**Headers:** `Authorization: Bearer {token}`

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "title": "Restored Task",
    ...
  },
  "message": "Task restored successfully"
}
```

---

### 🏷️ Category Endpoints

#### 1. Get All Categories

**Endpoint:** `GET /api/categories`

**Headers:** `Authorization: Bearer {token}`

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Work",
      "color": "#3B82F6",
      "tasks_count": 12,
      "created_at": "2025-12-19 10:00:00"
    },
    {
      "id": 2,
      "name": "Personal",
      "color": "#10B981",
      "tasks_count": 8,
      "created_at": "2025-12-19 10:00:00"
    }
  ]
}
```

#### 2. Create Category

**Endpoint:** `POST /api/categories`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "name": "New Category",
  "color": "#FF5733"
}
```

**Validation Rules:**
- name: required, max 255 characters, unique
- color: required, valid hex color format (#RRGGBB)

**Response (201 Created):**
```json
{
  "data": {
    "id": 8,
    "name": "New Category",
    "color": "#FF5733",
    "created_at": "2025-12-19 11:00:00"
  }
}
```

#### 3. Get Single Category

**Endpoint:** `GET /api/categories/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "name": "Work",
    "color": "#3B82F6",
    "created_at": "2025-12-19 10:00:00"
  }
}
```

---

## 🧪 Testing

### Running Tests

#### Run All Tests
```bash
php artisan test
```

#### Run Specific Test Suite
```bash
php artisan test --testsuite=Feature
```

#### Run Specific Test File
```bash
php artisan test tests/Feature/TaskTest.php
```

#### Run with Coverage Report
```bash
php artisan test --coverage
```

### Test Coverage

The project includes comprehensive feature tests covering:

#### AuthTest (6 tests)
- ✅ User registration with validation
- ✅ Duplicate email prevention
- ✅ User login functionality
- ✅ Invalid credentials handling
- ✅ User logout
- ✅ Token invalidation

#### TaskTest (12 tests)
- ✅ Authentication requirement
- ✅ Task creation with categories
- ✅ Input validation
- ✅ User can only see own tasks (Authorization)
- ✅ Filtering by status, priority, category
- ✅ Search functionality
- ✅ Task update
- ✅ Task deletion (soft delete)
- ✅ Cannot access other user's tasks
- ✅ Statistics endpoint
- ✅ Task restoration
- ✅ Observer logging (bonus feature)

#### CategoryTest (4 tests)
- ✅ List all categories
- ✅ Create category with validation
- ✅ Unique name constraint
- ✅ Color format validation

**Total Tests:** 22+ passing tests

### Sample Test Data

After running seeders, use these credentials for testing:

| Name | Email | Password |
|------|-------|----------|
| John Doe | john@example.com | password123 |
| Jane Smith | jane@example.com | password123 |
| Test User | test@example.com | password123 |

---

## 🎁 Bonus Features

### 1. API Rate Limiting ⭐

**Implementation:**
- Prevents API abuse and DDoS attacks
- User-based throttling using Sanctum tokens

**Configuration:**

| Endpoint | Limit | Window |
|----------|-------|--------|
| Login (`/api/login`) | 5 requests | per minute |
| General API endpoints | 60 requests | per minute |
| Per authenticated user | 10 requests | per minute |

**Rate Limit Headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
Retry-After: 60 (when limit exceeded)
```

**Error Response (429 Too Many Requests):**
```json
{
  "message": "Too Many Requests"
}
```

**Files Modified:**
- `routes/api.php` - Applied throttle middleware

### 2. Task Observer (Audit Logging) ⭐

**Purpose:**
- Automatic logging of task lifecycle events
- Improves debugging and accountability
- Tracks user actions for audit trails

**Logged Events:**
- Task creation (`created`)
- Task updates (`updated`)
- Task deletion (`deleted`)

**Implementation:**
- `app/Observers/TaskObserver.php` - Observer class
- `app/Providers/AppServiceProvider.php` - Observer registration

**Log Example:**
```
[2025-12-19 10:30:00] Task created: ID=19, Title="New Task", User=john@example.com
[2025-12-19 10:35:00] Task updated: ID=19, Changes={"status":"completed"}, User=john@example.com
[2025-12-19 10:40:00] Task deleted: ID=19, User=john@example.com
```

**Benefits:**
- Track all task modifications
- Debug production issues
- Meet compliance requirements
- User activity monitoring

---

## 📁 Project Structure

```
TaskFlow/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php      # Authentication logic
│   │   │       ├── TaskController.php       # Task CRUD + Statistics
│   │   │       └── CategoryController.php   # Category management
│   │   ├── Requests/
│   │   │   ├── StoreTaskRequest.php        # Task creation validation
│   │   │   ├── UpdateTaskRequest.php       # Task update validation
│   │   │   └── StoreCategoryRequest.php    # Category validation
│   │   └── Resources/
│   │       ├── TaskResource.php            # Task JSON formatting
│   │       ├── TaskCollection.php          # Task list formatting
│   │       ├── CategoryResource.php        # Category formatting
│   │       └── UserResource.php            # User formatting
│   ├── Models/
│   │   ├── User.php                        # User model with relationships
│   │   ├── Task.php                        # Task model with scopes
│   │   └── Category.php                    # Category model
│   ├── Observers/
│   │   └── TaskObserver.php                # Task lifecycle logging (BONUS)
│   └── Providers/
│       └── AppServiceProvider.php          # Observer registration
├── database/
│   ├── factories/
│   │   ├── TaskFactory.php                 # Task test data factory
│   │   └── CategoryFactory.php             # Category test data factory
│   ├── migrations/
│   │   ├── xxxx_create_categories_table.php
│   │   ├── xxxx_create_tasks_table.php
│   │   └── xxxx_create_category_task_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php              # Master seeder
│       ├── UserSeeder.php                  # Sample users
│       ├── CategorySeeder.php              # Sample categories
│       └── TaskSeeder.php                  # Sample tasks
├── routes/
│   └── api.php                             # API route definitions with rate limiting
├── tests/
│   └── Feature/
│       ├── AuthTest.php                    # Authentication tests
│       ├── TaskObserverTest.php            # Task Model Observer tests
│       ├── TaskTest.php                    # Task CRUD tests
│       └── CategoryTest.php                # Category tests
├── .env.example                            # Environment template
├── phpunit.xml                             # Test configuration
└── README.md                               # This file
```

---

## 💡 Design Decisions

### 1. **Soft Deletes for Tasks**
**Reason:** Allows task recovery and maintains data integrity for audit purposes.

### 2. **Many-to-Many Relationship (Tasks & Categories)**
**Reason:** Provides flexibility - tasks can belong to multiple categories, reflecting real-world usage.

### 3. **Query Scopes in Task Model**
**Reason:** Encapsulates common queries, improves code reusability, and keeps controllers clean.

### 4. **API Resources for Response Formatting**
**Reason:** Consistent JSON structure, easier API versioning, and cleaner controller code.

### 5. **Form Request Validation**
**Reason:** Separates validation logic from controllers, reusable, and follows single responsibility principle.

### 6. **Database Indexes**
**Reason:** Improves query performance on frequently filtered fields (user_id, status, due_date).

### 7. **Feature Branch Workflow**
**Reason:** Professional Git workflow, easier code review, and maintains clean commit history.

### 8. **Rate Limiting**
**Reason:** Production-ready security measure to prevent API abuse and ensure fair resource usage.

### 9. **Task Observer**
**Reason:** Clean separation of concerns, automatic logging without cluttering controllers.

---

## 🔒 Security Features

### Authentication
- ✅ Laravel Sanctum token-based authentication
- ✅ Password hashing with bcrypt
- ✅ Token expiration and revocation
- ✅ Protected routes with middleware

### Authorization
- ✅ Users can only access their own tasks
- ✅ Ownership verification before updates/deletes
- ✅ 404 responses for unauthorized access attempts

### Input Validation
- ✅ Strict validation rules on all inputs
- ✅ SQL injection prevention via Eloquent ORM
- ✅ XSS protection via Laravel's built-in escaping
- ✅ CSRF protection enabled

### Rate Limiting
- ✅ Login endpoint throttling (5/min)
- ✅ API endpoint throttling (60/min)
- ✅ Per-user rate limits for heavy task endpoint (10/min)
- ✅ DDoS attack mitigation

### Database Security
- ✅ Foreign key constraints
- ✅ Unique constraints
- ✅ Proper indexing
- ✅ Cascade delete prevention

---

## 🚀 Deployment Recommendations

### Production Checklist

```bash
# 1. Set environment to production
APP_ENV=production
APP_DEBUG=false

# 2. Optimize performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Database optimization
php artisan migrate --force

# 4. Security
# - Use HTTPS
# - Set strong APP_KEY
# - Configure CORS properly
# - Enable rate limiting
# - Setup database backups
```

---

## 📊 API Testing with Postman

### Quick Start

1. **Import Collection**
   - Create new Postman collection
   - Add base URL: `http://localhost:8000/api`

2. **Setup Environment Variables**
   ```
   base_url: http://localhost:8000/api
   token: (will be set after login)
   ```

3. **Authentication Flow**
   - Register → Get token
   - Login → Update token variable
   - Use {{token}} in Authorization header

### Sample Postman Requests

#### Register
```
POST {{base_url}}/register
Body (JSON):
{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
```
POST {{base_url}}/login
Body (JSON):
{
  "email": "john@example.com",
  "password": "password123"
}

Tests:
pm.environment.set("token", pm.response.json().access_token);
```

#### Get Tasks with Filters
```
GET {{base_url}}/tasks?status=pending&priority=high
Headers:
Authorization: Bearer {{token}}
```

---

## 🎯 Assignment Fulfillment Checklist

### Day 1 Requirements ✅
- [x] Project Setup (Laravel 12, Database, Sanctum)
- [x] Database Schema (Migrations with proper relationships)
- [x] Models & Relationships (User, Task, Category)
- [x] RESTful API Endpoints (Auth, Tasks, Categories)

### Day 2 Requirements ✅
- [x] Form Request Validation (Custom validation classes)
- [x] Advanced Features (Filtering, Search, Sort, Statistics, Soft Deletes)
- [x] API Resources (Formatted JSON responses)
- [x] Feature Testing (Comprehensive test coverage)
- [x] Documentation (Complete README with examples)

### Bonus Features Implemented 🎁
- [x] API Rate Limiting
- [x] Task Observer (Logging)

### Submission Requirements ✅
- [x] Code pushed to GitHub
- [x] Feature branch workflow used
- [x] Database seeders with sample data
- [x] .env.example file provided
- [x] Meaningful Git commit messages
- [x] Clean code with best practices

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 🙏 Acknowledgments

- **Laravel Framework** - For the excellent documentation and ecosystem
- **Laravel Sanctum** - For simple and secure API authentication
- **PHPUnit** - For comprehensive testing capabilities
- **All contributors** who helped test and review this project

---

## 📞 Support

For any questions or issues, please:
1. Check the [API Documentation](#api-documentation) above
2. Open an issue on GitHub
3. Contact via email

---

**Built with ❤️ using Laravel 12 | December 2025**