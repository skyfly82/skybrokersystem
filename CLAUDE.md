# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**SkyBrokerSystem** is a **Laravel 12-based** courier brokerage platform built on a **Modular Monolith** architecture. It manages shipments, payments, and integrations with multiple courier services. The system serves two main user types: system administrators and customers, each with dedicated panels and APIs.

## Development Commands (Server Environment)

### Environment Setup
1.  **Clone repository**: `git clone git@github.com:skyfly82/skybrokersystem.git`
2.  **Install dependencies**: `composer install`
3.  **Create environment file**: `cp .env.example .env`
4.  **Configure `.env`**: Set database credentials, application URL, and other required keys.
5.  **Generate app key**: `php artisan key:generate`
6.  **Run migrations and seeders**: `php artisan migrate --seed`
7.  **Link storage**: `php artisan storage:link`

### PHP/Laravel Commands
- **Install dependencies**: `composer install`
- **Run migrations**: `php artisan migrate`
- **Fresh migrations with seeding**: `php artisan migrate:fresh --seed`
- **Run seeders**: `php artisan seed`
- **Clear caches**: `php artisan optimize:clear` (clears config, route, view, and application cache)
- **Optimize for production**: `php artisan optimize` (caches configs and routes)

### Frontend Commands  
- **Install NPM dependencies**: `npm install`
- **Build for development**: `npm run dev` (also watches for changes)
- **Build for production**: `npm run build`

### Testing
- **Run tests**: `php artisan test`
- **Run with coverage**: `php artisan test --coverage`
- **Code Style Check**: `vendor/bin/pint --test`
- **Fix Code Style**: `vendor/bin/pint`
- **Testing framework**: PHPUnit

## Architecture Overview

### Multi-Guard Authentication System
The system uses Laravel's multi-guard authentication with two separate user types:
- **System Users** (`system_user` guard): Admin panel access with roles (admin, super_admin, marketing).
- **Customer Users** (`customer_user` guard): Customer panel access with company association.

### Service Layer Architecture
The system follows a service-oriented architecture with logic encapsulated in dedicated services:
- **Courier Services (`app/Services/Courier/`)**: Integrations with courier APIs (e.g., InPost).
- **Payment Services (`app/Services/Payment/`)**: Manages payment providers (PayNow, Stripe, Simulation).
- **SMS Services (`app/Services/SMS/`)**: Handles sending SMS via different providers (Twilio, SmsApi).
- **Notification Service (`app/Services/Notification/`)**: Orchestrates multi-channel notifications.

### Modular Route Structure
Routes are organized by domain in the `routes/api/` directory and loaded dynamically.
- `routes/api/auth.php`
- `routes/api/couriers.php`
- `routes/api/orders.php`
- `routes/api/payments.php`

### Validation and Authorization
- **Form Requests**: All write-operations in controllers use dedicated Form Requests for validation.
- **Policies**: Fine-grained authorization is handled by Policies (e.g., `ShipmentPolicy`, `OrderPolicy`).

### Middleware
- **Global**: `SecurityHeadersMiddleware`, `RateLimitMiddleware`.
- **Route-Specific**: `AdminMiddleware`, `CustomerActiveMiddleware`, `ApiKeyMiddleware`.

### Database
- **Primary Keys**: All models use **UUID v7** for primary keys to improve performance and scalability.
- **Migrations**: Atomic migrations located in `database/migrations/`.

### Configuration System
Central configuration in `config/skybrokersystem.php` covers:
- Service providers (couriers, payments, SMS)
- Security settings, timeouts, and feature flags
- API rate limiting and authentication details

## Important Development Notes

### API Key Management
- Customer API keys use the `sk_` prefix.
- API rate limiting is configured for **60 requests/minute** and **1000 requests/hour**.

### Code Quality
- **Laravel Pint** is used for enforcing a consistent code style. Run `vendor/bin/pint` to format code.
- **PHPStan** is used for static analysis to find potential bugs.

### Security Considerations
- Multi-guard authentication prevents privilege escalation between user types.
- API keys and Sanctum tokens provide layered security for the API.
- All external inputs must be validated through Form Requests.

This architecture provides a scalable foundation for courier brokerage operations with a clear separation of concerns and extensive integration capabilities.