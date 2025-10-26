# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Fantasy Academy is a full-stack application with a Symfony 7 API backend and a Vue 3 frontend. It's designed as a challenge/quiz platform where users can compete in fantasy-style challenges.

## Architecture

### Backend (API - Symfony 7)
- **Location**: `/api` directory
- **Framework**: Symfony 7.0+ with PHP 8.4+
- **Database**: PostgreSQL with Doctrine ORM
- **API**: API Platform 4.0 for REST endpoints
- **Authentication**: JWT via LexikJWTAuthenticationBundle
- **Architecture Pattern**: CQRS-like with Message/MessageHandler pattern
- **Key Components**:
  - `src/Entity/`: Doctrine entities (User, Challenge, Question, PlayerChallengeAnswer, PlayerAnsweredQuestion)
  - `src/Message/`: Command messages for operations (organized by domain: User, Challenge)
  - `src/MessageHandler/`: Command handlers implementing business logic (organized by domain)
  - `src/Api/`: API Platform resources organized by feature (each subdirectory contains Response DTOs and Provider classes)
    - Each API feature has its own directory (e.g., `Challenges/`, `ChallengeAnswers/`, `Leaderboards/`)
    - Contains Response DTOs (e.g., `ChallengeResponse.php`) defining API output structures
    - Contains Provider classes (e.g., `ChallengesProvider.php`) implementing API Platform state providers
  - `src/Repository/`: Custom Doctrine repositories
  - `src/Query/`: Query classes for complex database queries
  - `src/Services/`: Domain services and utilities
  - `src/Controller/`: Traditional Symfony controllers (used sparingly alongside API Platform)
  - `src/Value/`: Value objects
  - `src/FormData/`: Form data transfer objects
  - `src/FormType/`: Symfony form types 

### Frontend (Vue 3)
- **Location**: `/frontend` directory
- **Framework**: Vue 3 with TypeScript, Vite, Pinia, Tailwind CSS
- **Build Tool**: Vite
- **State Management**: Pinia
- **Styling**: Tailwind CSS

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

### Frontend (Vue)
```bash
# Run inside frontend container: docker compose exec frontend <command>

# Development server
pnpm dev

# Build for production
pnpm build

# Type checking
vue-tsc -b
```

## Service URLs
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

### Message/Handler Pattern
The API uses a message/handler pattern for write operations:
- Command messages in `src/Message/` define operations (e.g., `AnswerChallenge`)
- Handlers in `src/MessageHandler/` implement business logic
- Organized by domain (User, Challenge) for better structure
- Use Symfony Messenger for processing

### API Platform Integration
- API resources are organized by feature in `src/Api/` with each feature in its own directory
- Each feature directory contains:
  - Response DTO classes (e.g., `ChallengeResponse`) with API Platform attributes defining endpoints
  - Provider classes implementing `ProviderInterface` for data retrieval
- Providers often use raw SQL queries via Doctrine DBAL for optimized performance
- JWT authentication required for most endpoints
- Available API features: Challenges, ChallengeAnswers, ChallengeDetail, Leaderboards, LoggedUser, PlayerAnswers, PlayerInfo

## Code Quality & Testing

### Static Analysis
- **PHPStan**: Level max with bleeding edge rules enabled
- Extensions: Symfony, Doctrine, PHPUnit
- Configuration: `phpstan.neon`
- Always run on whole codebase: `composer phpstan` or `vendor/bin/phpstan --memory-limit=-1 analyse`
- Analyzes: `src/`, `bin/`, `tests/`, `config/`
- Never test single file(s) - always analyze the entire codebase

### Testing Strategy
The API uses a comprehensive testing approach with optimized database handling:

#### Test Structure
- **Location**: `tests/` directory mirrors `src/` structure
- **Test Types**:
  - API Integration Tests (`tests/Api/`) - Test API endpoints with full HTTP requests
  - Service Tests (`tests/Services/`) - Unit tests for service classes
- **Test Framework**: PHPUnit 12+ with ApiTestCase for API tests

#### Database Isolation
- **DAMA Doctrine Test Bundle**: Provides automatic transaction rollback per test
- **Database Caching**: Custom caching mechanism (`TestingDatabaseCaching.php`) speeds up test suite
  - Caches database state based on hash of migrations and fixtures
  - Avoids rebuilding database if no changes detected (24-hour cache)
  - Bootstrap process (`tests/bootstrap.php`):
    1. Drops and recreates test database
    2. Creates schema via `doctrine:schema:create` (faster than migrations)
    3. Loads fixtures via `doctrine:fixtures:load`
- **Fixtures**: Test data defined in `tests/DataFixtures/` (e.g., `UserFixture`, `CurrentChallenge1Fixture`)
  - Default test users: `admin@example.com` and `user@example.com` with password `pass`

#### Running Tests
```bash
# Run all tests
vendor/bin/phpunit

# Run specific test
vendor/bin/phpunit tests/Api/ChallengesTest.php
```

#### Testing Best Practices
- Each test runs in isolated database transaction (auto-rollback via DAMA)
- Use `TestingLogin::getJwt()` helper for authenticated requests
- API tests verify both HTTP status codes and response data structure
- Tests include coverage annotations (e.g., `@covers \FantasyAcademy\API\Api\Challenges\ChallengesProvider`)

### Important Constraints
- **Never execute doctrine migrations** unless explicitly asked - this is risky and might lead to data loss
- Tests use `doctrine:schema:create` instead of migrations for speed
- Always run PHPStan on whole codebase to catch type errors across the system
