# SkyBrokerSystem v2

Modern courier brokerage platform built with Symfony 7.3 and modern PHP practices.

## Project Overview

This is a complete rewrite of the original Laravel-based SkyBrokerSystem, now built on:
- **Symfony 7.3** - Latest stable PHP framework
- **Doctrine ORM** - Enterprise-grade data management with MySQL/MariaDB 8.0
- **API-First Architecture** - RESTful APIs with OpenAPI documentation
- **Modern Frontend** - React/Vue.js SPA (planned)
- **Production-Ready** - Direct deployment without Docker

## Development Status

🚧 **Under Development** - This project is currently being developed in phases:

### Phase 1: Core & Authentication (In Progress)
- [x] Symfony 7.3 project setup
- [x] MySQL/MariaDB 8.0 database configuration
- [x] Project structure migration (main app at /, Laravel legacy at /laravel/)
- [ ] Multi-guard authentication system
- [ ] Base entities and repositories
- [ ] API foundation with OpenAPI docs

### Phase 2: Business Logic (Planned)
- [ ] Courier service integrations (InPost, DHL)
- [ ] Order management system
- [ ] Payment processing
- [ ] Shipment tracking

### Phase 3: Frontend & UI (Planned)
- [ ] React/Vue.js admin panel
- [ ] Customer dashboard
- [ ] Mobile-responsive design
- [ ] Real-time notifications

### Phase 4: Advanced Features (Planned)
- [ ] Analytics and reporting
- [ ] CMS functionality
- [ ] Performance optimization
- [ ] Monitoring and logging

## Architecture

This project follows Domain-Driven Design (DDD) principles with:
- Clean architecture layers
- CQRS pattern for complex operations
- Event-driven communication
- Microservices-ready structure

## System Architecture

### Current Deployment
- **Main Application**: http://185.213.25.106/ (Symfony 7.3)
- **Legacy System**: http://185.213.25.106/laravel/ (Original Laravel system)
- **Database**: MySQL/MariaDB 8.0
- **PHP**: 8.3+

## Original System

The original Laravel-based system can be found at [skyfly82/skybrokersystem](https://github.com/skyfly82/skybrokersystem) and is preserved at `/laravel/` path.

## License

Proprietary - All rights reserved.