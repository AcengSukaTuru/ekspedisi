# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application for an expedition/shipping management system (ekspedisi). The system manages shipments, payments, courier/vehicle assignments, delivery attempts, and tracking for a logistics company with three user roles.

The application is intentionally kept as a **Laravel monolith** for school exam/demo needs: Blade-rendered pages, Tailwind styling, Alpine.js for light interactivity, MySQL via XAMPP, and no separate React/Vue frontend.

## Current Product Scope

The app now includes:

- Role-based dashboards for admin, courier, and customer.
- Professional FastExpress landing page.
- Public tracking page by tracking number.
- Customer shipment creation with drop-off / pickup request.
- Prepaid and COD payment flows.
- Admin payment verification.
- Courier/vehicle assignment through `ShipmentAssignment`.
- Delivery attempts with failed delivery, reschedule, and return-to-sender flow.
- Browser-printable invoice and shipping label.
- Admin operational dashboard with KPIs, status distribution, recent payments, attention list, and quick actions.
- Dark mode with instant toggle.

## Development Commands

### Initial Setup
```bash
composer setup
```
This runs dependency install, `.env` creation, app key generation, migrations, and frontend build.

### Local Development Server
```bash
composer dev
```
Runs four concurrent processes:
- `php artisan serve` - Laravel application server
- `php artisan queue:listen` - Queue worker
- `php artisan pail` - Real-time log viewer
- `npm run dev` - Vite dev server with hot reload

### Simple Exam/Demo Run

For a simple demo after dependencies, database, and assets are ready:
```bash
php artisan serve
```

If assets changed, build once first:
```bash
npm run build
php artisan serve
```

### Testing
```bash
composer test
php artisan test
php artisan test --filter=TestName
```

Current feature coverage includes:
- `PublicTrackingTest`
- `ShipmentPrintTest`
- `AdminDashboardTest`
- `CompleteExpeditionWorkflowTest`
- `AuthorizationTest`
- `Fase1ShipmentAssignmentTest`
- Breeze auth/profile tests

### Code Quality
```bash
vendor/bin/pint
```

### Database
```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed
php artisan tinker
```

Local development/demo should use **XAMPP MySQL**. Do not rely on leftover local SQLite databases for app demo data.

## Demo Accounts

Seeders create demo users for presentation. Password is `password` for all accounts.

| Role | Email | Password |
|---|---|---|
| Admin | `admin@ekspedisi.test` | `password` |
| Courier | `kurir1@ekspedisi.test` | `password` |
| Customer | `customer@ekspedisi.test` | `password` |

Additional seeded demo accounts may exist for more complete data, such as `admin2@ekspedisi.test`, `kurir2@ekspedisi.test`, `kurir3@ekspedisi.test`, `customer2@ekspedisi.test`, and `toko@ekspedisi.test`.

## Architecture

### High-Level Pattern

```text
Routes
  ↓
Middleware role / public route
  ↓
Controller
  ↓
Policy / Gate authorization
  ↓
Service / Eloquent model
  ↓
Database
  ↓
Blade view
```

This is not an API-first SPA. Keep features server-rendered with Blade unless there is a clear reason not to.

### Frontend Stack

- **Blade**: Server-rendered views.
- **Tailwind CSS**: Styling and responsive layout.
- **Alpine.js**: Small interactions such as theme toggle, sidebar, dropdowns, and collapsible panels.
- **Vite**: Asset bundling via `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
- **Laravel Breeze**: Authentication scaffolding.

Do not introduce React, Vue, Inertia, or Livewire unless the user explicitly asks for a stack change. For this project, professional UI should be achieved by improving Blade/Tailwind/Alpine first.

### UI System

Reusable CSS component classes are defined in [resources/css/app.css](resources/css/app.css):

- `.card`, `.card-hover`, `.card-glass`
- `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-ghost`, `.btn-sm`
- `.input`, `.select`, `.textarea`
- `.badge`
- `.table-wrapper`, `.table`
- `.sidebar-link`, `.sidebar-link-active`
- `.step-dot`, `.step-line`

Use these classes before inventing one-off utility stacks.

### Layouts

- Authenticated app layout: [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php)
- Guest/auth layout: [resources/views/layouts/guest.blade.php](resources/views/layouts/guest.blade.php)
- Sidebar: [resources/views/layouts/sidebar.blade.php](resources/views/layouts/sidebar.blade.php)
- Navbar: [resources/views/layouts/navbar.blade.php](resources/views/layouts/navbar.blade.php)
- Landing page: [resources/views/landing.blade.php](resources/views/landing.blade.php)

The landing page should use the **FastExpress** identity and blue/slate design language, not the old `SHIPIT` red/black template.

## Role-Based System

The application has three role groups:

- **Admin** (`/admin/*`): Manages all resources, verifies payments, assigns couriers/vehicles, updates shipment statuses, reschedules failed deliveries, prints invoice/label.
- **Courier** (`/courier/*`): Views assigned shipments, updates tracking, records delivery attempts.
- **Customer** (`/customer/*`): Creates shipments, views own shipments/tracking, submits payment proof, prints own invoice/label.

Role checking is done via:
- Middleware: `RoleMiddleware` (`role:admin`, `role:courier`, `role:customer`)
- Model methods: `$user->isAdmin()`, `$user->isCourier()`, `$user->isCustomer()`
- Constants: `User::ROLE_ADMIN`, `User::ROLE_COURIER`, `User::ROLE_CUSTOMER`

## Routing

Main routes are in [routes/web.php](routes/web.php).

### Public Routes

Public routes live outside `auth` middleware:

- `/` → landing page
- `/tracking` → public tracking search
- `/tracking/{trackingNumber}` → public tracking result

Public tracking routes are throttled and should stay read-only.

### Authenticated Role Routes

- `/admin/*` → admin features
- `/customer/*` → customer features
- `/courier/*` → courier features

For role-specific shipment actions, route names follow this pattern:

- `admin.shipments.show`
- `customer.shipments.show`
- `courier.shipments.show`
- `admin.shipments.invoice`
- `customer.shipments.invoice`
- `admin.shipments.label`
- `customer.shipments.label`

## Authorization

Authorization uses Laravel policies / Gate checks:

- `ShipmentPolicy`: Controls shipment visibility and shipment operations.
- `PaymentPolicy`: Controls payment access and verification.

Always authorize model actions in controllers:

```php
Gate::authorize('view', $shipment);
Gate::authorize('updateStatus', $shipment);
```

or equivalent `$this->authorize(...)`.

Do not duplicate loose manual access checks in controllers when a policy already covers the action.

## Domain Model Relationships

### Core Entities

- **User**: Has role. Customers have a `customer()` relation.
- **Customer**: Belongs to User, has many Shipments and Payments through shipments.
- **Shipment**: Core entity with sender/receiver details, branches, items, payment, tracking history, assignment history, delivery attempts.
- **ShipmentAssignment**: Courier/vehicle assignment history. A shipment can have many historical assignments but only one `activeAssignment()`.
- **ShipmentTracking**: Historical status/location updates.
- **DeliveryAttempt**: Records courier delivery attempt success/failure.
- **Payment**: 1:1 with Shipment, supports prepaid and COD.
- **Branch**: Origin/destination branch.
- **Vehicle**: Vehicle used through ShipmentAssignment.
- **Rate**: Pricing by origin, destination, and service type.

### Key Relationships

- Shipment → Customer (`belongsTo`)
- Shipment → originBranch / destinationBranch (`belongsTo Branch`)
- Shipment → shipmentItems (`hasMany`)
- Shipment → payment (`hasOne`)
- Shipment → shipmentTrackings (`hasMany`)
- Shipment → deliveryAttempts (`hasMany`)
- Shipment → assignments (`hasMany ShipmentAssignment`)
- Shipment → activeAssignment (`hasOne ShipmentAssignment where status='active'`)
- ShipmentAssignment → courier (`belongsTo User`)
- ShipmentAssignment → vehicle (`belongsTo Vehicle`)
- Vehicle → activeAssignments (`hasMany ShipmentAssignment where status='active'`)

The old `shipments.vehicle_id` column still exists for backward compatibility. New operational code should prefer `activeAssignment.vehicle` and `assignments`.

## Shipment Lifecycle

Statuses are defined in [app/Models/Shipment.php](app/Models/Shipment.php) and must use constants, not magic strings.

Current statuses:

1. `created` - Order created by customer.
2. `picked_up` - Package received or picked up.
3. `at_origin_hub` - Package arrived at origin hub.
4. `in_transit` - Package moving between hubs/cities.
5. `at_dest_hub` - Package arrived at destination hub.
6. `out_for_delivery` - Courier is delivering to receiver.
7. `delivered` - Final successful status.
8. `failed_delivery` - Delivery attempt failed.
9. `returned_to_sender` - Final return-to-sender status.

Valid flow:

```text
created → picked_up
picked_up → at_origin_hub | in_transit
at_origin_hub → in_transit
in_transit → at_dest_hub
at_dest_hub → out_for_delivery
out_for_delivery → delivered | failed_delivery
failed_delivery → out_for_delivery | returned_to_sender
delivered → final
returned_to_sender → final
```

Use:

```php
Shipment::STATUS_CREATED
Shipment::STATUS_PICKED_UP
Shipment::STATUS_AT_ORIGIN_HUB
Shipment::STATUS_IN_TRANSIT
Shipment::STATUS_AT_DEST_HUB
Shipment::STATUS_OUT_FOR_DELIVERY
Shipment::STATUS_DELIVERED
Shipment::STATUS_FAILED_DELIVERY
Shipment::STATUS_RETURNED_TO_SENDER
```

Do **not** use old status names like `pending` or `processed` in new code.

## Shipment Creation

Shipment creation logic lives in [app/Services/ShipmentService.php](app/Services/ShipmentService.php). Reuse this service when creating shipments from customer flows.

The service handles:

- Tracking number generation.
- Shipping cost calculation.
- Shipment record creation.
- Shipment item creation.
- Payment creation.
- Initial tracking entry.

## Public Tracking

Implemented through:

- [app/Http/Controllers/TrackingController.php](app/Http/Controllers/TrackingController.php)
- [resources/views/trackings/public.blade.php](resources/views/trackings/public.blade.php)
- Routes `/tracking` and `/tracking/{trackingNumber}`

Public tracking must only expose safe shipment data:

Allowed:
- tracking number
- status label
- origin/destination city or branch name
- service type
- shipment date / estimated arrival
- tracking timeline location and description

Forbidden on public page:
- customer internal data
- sender/receiver phone
- full sender/receiver address
- payment proof
- payment verification notes
- item photos

Feature test: [tests/Feature/PublicTrackingTest.php](tests/Feature/PublicTrackingTest.php)

## Invoice and Shipping Label Print

Implemented as browser-print HTML, not PDF package. This keeps deployment simple.

Files:

- [resources/views/shipments/invoice.blade.php](resources/views/shipments/invoice.blade.php)
- [resources/views/shipments/label.blade.php](resources/views/shipments/label.blade.php)
- Methods in [app/Http/Controllers/ShipmentController.php](app/Http/Controllers/ShipmentController.php): `invoice()` and `label()`

Routes exist for admin and customer:

- `admin.shipments.invoice`
- `admin.shipments.label`
- `customer.shipments.invoice`
- `customer.shipments.label`

Print pages must authorize with `Gate::authorize('view', $shipment)`. Admin can print any shipment; customer can only print their own shipment.

Do not add DomPDF/Snappy unless explicitly requested. Browser print is the current project standard.

Feature test: [tests/Feature/ShipmentPrintTest.php](tests/Feature/ShipmentPrintTest.php)

## Admin Dashboard

Admin dashboard is now separate from customer/courier dashboard.

- Admin view: [resources/views/dashboard/admin.blade.php](resources/views/dashboard/admin.blade.php)
- Shared customer/courier view: [resources/views/dashboard/index.blade.php](resources/views/dashboard/index.blade.php)
- Controller: [app/Http/Controllers/DashboardController.php](app/Http/Controllers/DashboardController.php)

Admin dashboard includes:

- KPI cards:
  - Total Shipment
  - Shipment Aktif
  - Sudah Sampai
  - Gagal / RTS
  - Payment Pending
  - Revenue Paid
- Shipment Terbaru table.
- Distribusi Status horizontal bars.
- Butuh Tindakan list.
- Payment Terbaru panel.
- Quick actions to shipments, payments, rates, and vehicles.

Feature test: [tests/Feature/AdminDashboardTest.php](tests/Feature/AdminDashboardTest.php)

## Delivery Attempts

Delivery attempts are part of the courier delivery flow.

- Courier can record attempt only for assigned shipment.
- Successful attempt moves shipment to `delivered` and completes active assignment.
- Failed attempt moves shipment to `failed_delivery`.
- Admin can reschedule failed delivery back to `out_for_delivery` while attempts remain.
- After maximum failed attempts, shipment returns to sender.

Use `DeliveryAttempt` helpers and `Shipment` helpers:

- `$shipment->failedAttemptCount()`
- `$shipment->remainingAttempts()`
- `$shipment->canAttemptDelivery()`
- `$shipment->shouldReturnToSender()`

## Payment Flow

Payment supports:

- `Payment::TYPE_PREPAID`
- `Payment::TYPE_COD`

Payment statuses:

- `Payment::STATUS_PENDING`
- `Payment::STATUS_PAID`
- `Payment::STATUS_FAILED`

Customer submits proof for prepaid. Admin verifies. COD can be collected by courier and marked paid through the COD collection flow.

## Testing Conventions

Tests use PHPUnit with `RefreshDatabase`. Tests should create related models explicitly unless a factory exists.

Use factories for users:

```php
User::factory()->create(['role' => User::ROLE_ADMIN]);
```

Most domain entities are created manually in tests:

```php
Branch::create([...]);
Customer::create([...]);
Shipment::create([...]);
Payment::create([...]);
ShipmentAssignment::create([...]);
```

When adding features, add a focused feature test under [tests/Feature](tests/Feature).

Recommended test patterns:

- Public page access for guests.
- Role-based access for admin/customer/courier.
- Private data is not leaked on public pages.
- Policy-protected routes return 403 for unauthorized users.
- Browser-print pages render expected fields.

## Key Files to Understand

- [routes/web.php](routes/web.php) - Public and role-based routing.
- [app/Models/User.php](app/Models/User.php) - Role helpers and constants.
- [app/Models/Shipment.php](app/Models/Shipment.php) - Shipment domain model, statuses, relationships, delivery attempt helpers.
- [app/Models/Payment.php](app/Models/Payment.php) - Payment types/statuses and labels.
- [app/Models/ShipmentAssignment.php](app/Models/ShipmentAssignment.php) - Courier/vehicle assignment history.
- [app/Http/Controllers/DashboardController.php](app/Http/Controllers/DashboardController.php) - Role dashboards.
- [app/Http/Controllers/ShipmentController.php](app/Http/Controllers/ShipmentController.php) - Shipment CRUD, status update, invoice/label print.
- [app/Http/Controllers/TrackingController.php](app/Http/Controllers/TrackingController.php) - Authenticated tracking and public tracking.
- [app/Http/Controllers/DeliveryAttemptController.php](app/Http/Controllers/DeliveryAttemptController.php) - Delivery attempt and reschedule flow.
- [app/Http/Middleware/RoleMiddleware.php](app/Http/Middleware/RoleMiddleware.php) - Role-based route access.
- [app/Http/Controllers/Concerns/InteractsWithShipments.php](app/Http/Controllers/Concerns/InteractsWithShipments.php) - Shared controller helpers.
- [resources/css/app.css](resources/css/app.css) - UI component classes.
- [ALUR_EKSPEDISI.md](ALUR_EKSPEDISI.md) - Full product flow documentation.

## Common Patterns

### Adding a New Feature

1. Add migration if database changes are needed.
2. Update/create Eloquent model relationships and casts.
3. Add/update Policy for authorization.
4. Add controller methods.
5. Add public or role-prefixed route in [routes/web.php](routes/web.php).
6. Create Blade view using existing UI classes.
7. Add feature tests.
8. Run `php artisan test`.
9. Run `npm run build` if frontend assets changed.

### Querying Shipments by Role

- Admin: all shipments.
- Customer: shipments where `customer_id` matches `$user->customer->id`.
- Courier: shipments where `activeAssignment.courier_id` matches current user.

### Role-Specific Links in Blade

Use role checks to pick route names:

```php
$route = auth()->user()->isAdmin()
    ? 'admin.shipments.show'
    : 'customer.shipments.show';
```

### Public vs Private Pages

Public pages should never reuse authenticated detail views directly because those views contain private addresses, phone numbers, payment details, and operational actions.

Use separate public views with explicitly limited data.

### Status Constants

Always use model constants:

```php
Shipment::STATUS_CREATED
Payment::STATUS_PENDING
```

Avoid magic strings in new PHP code unless matching request validation values or view-only labels.
