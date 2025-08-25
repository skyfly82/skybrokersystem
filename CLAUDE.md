# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**SkyBrokerSystem v6** is a Laravel 11-based courier brokerage platform that manages shipments, payments, and integrations with multiple courier services. The system serves two main user types: system administrators and customers, each with dedicated panels and APIs.

## Development Commands

### Environment Setup
- **Docker setup**: `make setup` - Initial setup with containers, migrations, and seeding
- **Start development**: `make dev` - Starts development environment with additional tools
- **Build containers**: `make build`
- **Start services**: `make up`
- **Stop services**: `make down`

### PHP/Laravel Commands
- **Install dependencies**: `make install` or `docker-compose exec app composer install`
- **Run migrations**: `make migrate` or `docker-compose exec app php artisan migrate`
- **Fresh migrations**: `make migrate-fresh` - Drops all tables and rebuilds
- **Run seeders**: `make seed`
- **Clear caches**: `make clear-cache` - Clears config, route, view, and application cache
- **Optimize for production**: `make optimize` - Caches configs and routes

### Frontend Commands  
- **Install NPM dependencies**: `make npm-install`
- **Build for development**: `make npm-dev`
- **Build for production**: `make npm-build`
- **Watch for changes**: `npm run dev` (run directly or via `docker-compose exec app npm run dev`)

### Testing
- **Run tests**: `make test` or `docker-compose exec app php artisan test`
- **Run with coverage**: `make test-coverage`
- **Testing framework**: PHPUnit with Laravel testing features

### Database Access
- **MySQL shell (root)**: `make mysql`
- **MySQL shell (app user)**: `make mysql-user`
- **Redis CLI**: `make redis`
- **Container shell**: `make shell`

## Architecture Overview

### Multi-Guard Authentication System
The system uses Laravel's multi-guard authentication with two separate user types:
- **System Users** (`system_user` guard): Admin panel access with roles (admin, super_admin)
- **Customer Users** (`customer_user` guard): Customer panel access with company association

### Core Models & Relationships
- **Customer**: Main company entity with credit limits, API keys, and status
- **CustomerUser**: Individual users belonging to a customer company
- **Shipment**: Core shipment entity with courier integration
- **Payment**: Payment processing with multiple provider support
- **Transaction**: Financial transaction records
- **Notification**: System notifications with template support

### Service Architecture
The system follows a service-oriented architecture with dedicated services:

#### Courier Services (`app/Services/Courier/`)
- **CourierServiceInterface**: Contract for all courier providers
- **InPostService**: Integration with InPost API
- Located in `app/Services/Courier/Providers/`

#### Payment Services (`app/Services/Payment/`)  
- **PaymentProviderInterface**: Contract for payment providers
- **Providers**: PayNowProvider, StripeProvider, SimulationProvider
- **Payment.php**: Core payment processing service

#### SMS Services (`app/Services/SMS/`)
- **SmsProviderInterface**: Contract for SMS providers  
- **Providers**: TwilioProvider, SmsApiProvider, LogProvider
- **SmsManager**: Manages SMS sending and provider selection

#### Notification Service (`app/Services/Notification/`)
- **NotificationService**: Handles multi-channel notifications
- **Channels**: EmailChannel, SmsChannel with dedicated interfaces

### Route Structure

#### Admin Routes (`/admin`)
- Dashboard, customers, shipments, payments, notifications management
- Reports, courier settings, system settings, user management
- Protected by `auth:system_user` and `admin` middleware

#### Customer Routes (`/customer`) 
- Dashboard, shipment creation/tracking, payment management
- Profile management, team user management (for admin users)
- Protected by `auth:customer_user` and `customer.active` middleware

#### API Routes (`/api/v1`)
- **Public**: Health check, courier info, public tracking
- **Protected**: Customer info, shipment management, payment processing
- Two authentication methods: API key (`api.key` middleware) and Sanctum tokens

### Middleware
- **AdminMiddleware**: Role-based admin access control  
- **ApiKeyMiddleware**: API key authentication for external integrations
- **CustomerActiveMiddleware**: Ensures customer account is active
- **RateLimitMiddleware**: API rate limiting

### Configuration System
Extensive configuration in `config/skybrokersystem.php` covering:
- Customer settings (auto-approval, credit limits, API keys)
- Payment provider configuration and limits
- Courier service integrations and timeouts  
- Notification channels and templates
- API rate limiting and authentication
- Security, caching, and performance settings
- Feature flags for enabling/disabling functionality

### Database Migrations
Key migrations in `database/migrations/`:
- User and authentication tables
- Customer and customer_user tables  
- Courier services configuration
- Shipments with courier integration fields
- Payments and transactions for financial tracking

### Background Processing
- **Jobs**: Email and SMS notifications (`app/Jobs/`)
- **Console Commands**: Daily reports, SMS provider testing
- **Queue Configuration**: Uses Redis/database for job processing

### Frontend Stack
- **Views**: Blade templates with separate admin and customer layouts
- **Styling**: Tailwind CSS with custom components
- **JavaScript**: Alpine.js for interactivity, Chart.js for analytics
- **Build System**: Vite for asset compilation

## Important Development Notes

### API Key Management
- Customer API keys use `sk_` prefix with 48-character length
- API rate limiting: 1000 requests/hour, 60/minute by default
- Webhook endpoints have no rate limiting for external services

### Payment Flow
- Supports simulation, PayNow, and Stripe providers
- Simulation provider auto-completes payments under 100 PLN
- Payment timeout: 24 hours
- VAT rate configured at 23%

### Courier Integration
- Primary integration with InPost, framework for additional providers
- Webhook support for status updates from courier APIs
- Label generation in PDF format (A4 size default)
- Tracking updates every 60 minutes

### Notification System  
- Multi-channel: email, SMS, database notifications
- Template-based system with caching
- Queue-based processing with retry logic (3 attempts)
- SMS development mode for testing

### Security Considerations
- Multi-guard authentication prevents cross-contamination
- API key authentication for external integrations
- Webhook signature validation enabled
- File upload restrictions and virus scanning support
- HTTPS enforcement available for API endpoints

### Performance Features
- Extensive caching for courier services, pickup points, pricing
- Database query caching available
- Response compression and CDN support
- View, route, and config caching for production

This architecture provides a scalable foundation for courier brokerage operations with clear separation of concerns and extensive integration capabilities.
- nie wprowadzaj zmian do Dcokera poniewa piszemy od razu na serwerze