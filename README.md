# Analytics Widget (Laravel 12)

Author: Manohar Zarkar

## Overview
A production-style analytics dashboard built with Laravel 12. It shows headline metrics and time-series charts for users, revenue, orders, and growth. The stack is complete: service layer, validation, resources, authorization, seeding, factories, tests, API and web routes, caching, CSV export, and a responsive UI with Chart.js.

## Features
- Responsive Blade UI with modular KPI component
- Chart.js charts for users, revenue, orders, and growth
- Service layer (AnalyticsService) with real Eloquent aggregates
- Controller: validation, caching, JSON resource, CSV export
- API route mirroring the web payload
- Authorization gate protecting web and API
- Config for cache TTL
- Order model, migration, factory, and realistic seeding
- Tests: unit (service) and feature (controller)
- Artisan command to export analytics CSV

## Key Files
- Services: app/Services/AnalyticsService.php
- Controller: app/Http/Controllers/AnalyticsController.php
- Request: app/Http/Requests/AnalyticsDataRequest.php
- Resource: app/Http/Resources/AnalyticsResource.php
- Authorization: app/Providers/AuthServiceProvider.php (Gate: viewAnalytics)
- Model: app/Models/Order.php
- Migration: database/migrations/2025_09_24_000000_create_orders_table.php
- Factory: database/factories/OrderFactory.php
- Seeder: database/seeders/DatabaseSeeder.php
- Console Command: app/Console/Commands/ExportAnalyticsCommand.php
- Web routes: routes/web.php
- API routes: routes/api.php
- Layout: resources/views/layouts/app.blade.php
- Dashboard: resources/views/analytics/index.blade.php
- Blade component: resources/views/components/kpi-card.blade.php
- Tests: tests/Unit/AnalyticsServiceTest.php, tests/Feature/AnalyticsControllerTest.php

## Setup
1) Install dependencies and copy env
```
composer install
cp .env.example .env
php artisan key:generate
```

2) Migrate and seed
```
php artisan migrate --force
php artisan db:seed --force
```

3) Serve
```
php artisan serve
```

## Using the App
- Web dashboard: GET /analytics
- API data: GET /api/analytics?days=30
- CSV export (web): GET /analytics/export?days=30
- CSV export (CLI):
```
php artisan analytics:export --days=30
```

## Authorization
All analytics routes (web and API) are protected by the viewAnalytics Gate in AuthServiceProvider. For demo, it currently returns true. Replace with your own logic (roles or permissions) as needed.

## Configuration
- Cache TTL (seconds): config/analytics.php
- Override via .env:
```
ANALYTICS_CACHE_TTL=30
```

## Data Model
orders table:
- user_id (nullable, foreign key)
- total (decimal 12,2)
- status (string; e.g., paid)
- paid_at (nullable timestamp)

users table is Laravel default. Time-series aggregates group by day.

## Testing
Run the full test suite:
```
php artisan test
```
- Unit: validates service metrics and time-series generation
- Feature: verifies dashboard renders and JSON structure for the data endpoint

## Code Style
- PSR-12, strict types enabled across PHP files
- Concise docblocks
- Controller stays thin; business logic in the service

## Notes for Reviewers
- Real Eloquent queries using date ranges and daily grouping
- Growth calculated period-over-period and day-over-day
- Caching via Cache::remember with configurable TTL
- Routes protected by Gate to demonstrate auth integration
- CSV export implemented as web response and as an Artisan command

## Author
Manohar Zarkar - implemented all code and structure in this repository.
