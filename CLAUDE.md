# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Fantasy Academy is a full-stack application with a Symfony 7 API backend and multiple frontend options (Next.js React and Vue 3). It's designed as a challenge/quiz platform where users can compete in fantasy-style challenges.

## Architecture

### Backend (API - Symfony 7)
- **Location**: `/api` directory
- **Framework**: Symfony 7.0+ with PHP 8.4+
- **Database**: PostgreSQL with Doctrine ORM
- **API**: API Platform 4.0 for REST/GraphQL endpoints
- **Authentication**: JWT via LexikJWTAuthenticationBundle
- **Architecture Pattern**: CQRS-like with Message/MessageHandler pattern
- **Key Components**:
  - `src/Entity/`: Doctrine entities (User, Challenge, Question, etc.)
  - `src/Message/`: Command messages for operations
  - `src/MessageHandler/`: Command handlers implementing business logic
  - `src/Api/Response/`: API response DTOs
  - `src/Api/StateProvider/`: API Platform state providers
  - `src/Repository/`: Custom Doctrine repositories 

### Frontend Options
1. **Next.js (React)**: `/frontend` - Next.js 15 with TypeScript, Tailwind CSS, NextAuth
2. **Vue 3**: `/frontend-vue` - Vue 3 with TypeScript, Vite, Pinia, Tailwind CSS

Both frontends consume the same Symfony API.

## Development Commands

### Environment Setup
```bash
# Start all services (API, frontend, database, etc.)
docker compose up

# Load test fixtures (creates admin@example.com and user@example.com with password 'pass')
docker compose exec api bin/console doctrine:fixtures:load

# Create admin user manually
docker compose exec api bin/console app:user:register admin@admin.com admin
```

### API (Symfony)

```bash
# Run inside api container: docker compose exec api <command>

# PHPStan static analysis
composer phpstan
# or directly: vendor/bin/phpstan --memory-limit=-1 analyse

# PHPUnit tests
vendor/bin/phpunit

# Doctrine migrations
bin/console doctrine:migrations:migrate

# Clear cache
bin/console cache:clear
```

### Frontend (Next.js)
```bash
# Run inside api container: docker compose exec frontend <command>

# Development server
pnpm dev

# Build for production
pnpm build

# Lint code
pnpm lint

# Run Playwright tests
pnpm test
```

### Frontend (Vue)
```bash
# Run inside frontend-vue container: docker compose exec frontend-vue <command>

# Development server
pnpm dev

# Build for production
pnpm build

# Type checking
vue-tsc -b
```

## Service URLs
- **Frontend (Next.js)**: http://localhost:3000
- **Frontend (Vue)**: http://localhost:5173
- **API**: http://localhost:8080
- **Database Admin (Adminer)**: http://localhost:8000
- **Mail Catcher**: http://localhost:8025

## Database Access
- **Host**: localhost:5432
- **Database**: fantasy_academy
- **User**: postgres
- **Password**: postgres

## Key Domain Concepts

### Entities
- **User**: Application users with authentication
- **Challenge**: Quiz-like challenges with multiple questions
- **Question**: Individual questions within challenges (choice or numeric types)
- **PlayerChallengeAnswer**: User's answers to challenges
- **PlayerAnsweredQuestion**: Individual question answers

### Message Pattern
The API uses a message/handler pattern for operations:
- Commands in `src/Message/` define operations
- Handlers in `src/MessageHandler/` implement business logic
- Use Symfony Messenger for processing

### API Platform Integration
- State providers in `src/Api/StateProvider/` customize API responses
- Response DTOs in `src/Api/Response/` shape API output
- JWT authentication required for most endpoints

## Code Quality Tools
- **PHPStan**: Level max with Symfony, Doctrine, and PHPUnit extensions
- **PHPUnit**: Unit and integration tests with DAMA test bundle for database isolation
- **ESLint**: Code linting for frontend projects

## Testing Approach
- API tests use PHPUnit with database fixtures
- Frontend tests use Playwright for E2E testing
- Database transactions are isolated per test using DAMA doctrine test bundle

- Never execute doctrine migrations unless explicitely asked to - this is risky and might lead into data loss
