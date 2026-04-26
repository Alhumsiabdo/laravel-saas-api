# laravel-saas-api

[![Tests](https://github.com/Alhumsiabdo/laravel-saas-api/actions/workflows/tests.yml/badge.svg)](https://github.com/Alhumsiabdo/laravel-saas-api/actions)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

A production-grade **multi-tenant REST API** built with Laravel 11.  
Demonstrates: Sanctum authentication, workspace-scoped multi-tenancy,  
service layer pattern, Redis caching, and comprehensive feature tests.

## Tech Stack

- **PHP 8.3** + **Laravel 11**
- **MySQL 8** — primary database
- **Redis** — caching and queues
- **Laravel Sanctum** — API authentication
- **Docker** + **Laravel Sail** — local development
- **GitHub Actions** — CI on every push

## Architecture

Request → FormRequest (validation)
→ Controller (thin, delegates only)
→ Service (all business logic)
→ Model (relationships + fillable)
→ Resource (response formatting)

Every API endpoint is scoped through the authenticated user's workspaces.  
A user can never access data from a workspace they don't belong to.

## Quick Start

```bash
git clone https://github.com/Alhumsiabdo/laravel-saas-api.git
cd laravel-saas-api
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

API is now running at `http://localhost/api`

## API Endpoints

### Auth
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/register` | Register new user | No |
| POST | `/api/login` | Login and get token | No |
| POST | `/api/logout` | Logout current token | Yes |

### Workspaces
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/workspaces` | List my workspaces | Yes |
| POST | `/api/workspaces` | Create workspace | Yes |
| GET | `/api/workspaces/{id}` | Get workspace | Yes |
| PUT | `/api/workspaces/{id}` | Update workspace | Yes |
| DELETE | `/api/workspaces/{id}` | Delete workspace | Yes |

### Projects
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/workspaces/{id}/projects` | List projects | Yes |
| POST | `/api/workspaces/{id}/projects` | Create project | Yes |
| GET | `/api/projects/{id}` | Get project | Yes |
| PUT | `/api/projects/{id}` | Update project | Yes |
| DELETE | `/api/projects/{id}` | Delete project | Yes |

### Tasks
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/projects/{id}/tasks` | List tasks | Yes |
| POST | `/api/projects/{id}/tasks` | Create task | Yes |
| GET | `/api/tasks/{id}` | Get task | Yes |
| PUT | `/api/tasks/{id}` | Update task | Yes |
| DELETE | `/api/tasks/{id}` | Delete task | Yes |

## Testing

```bash
php artisan test
```

25 tests · 49 assertions · all passing

## Key Technical Decisions

**Why a Service layer?**  
Controllers only validate input and return responses. All business logic  
lives in dedicated Service classes — clean, testable, and easy to extend.

**Why workspace-scoped multi-tenancy?**  
Every query flows through the authenticated user's workspace memberships.  
Policies enforce access at the model level — no data leaks between tenants.

**Why SQLite for tests?**  
In-memory SQLite makes the test suite run in under 1 second with zero  
external dependencies. CI passes without a real MySQL container.

## License

MIT © [Abdullah Alhumsi](https://github.com/Alhumsiabdo)
