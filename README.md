# Group Hospitality CRM & Loyalty Platform

A comprehensive Laravel-based CRM system for hospitality groups with multiple outlets (hotels, resorts, bars, restaurants, clubs). Features a unified customer database, QR code registration, loyalty program, campaigns, and analytics.

## Features

### Core Features
- **Unified Customer Database**: Single shared CRM across all outlets supporting both corporate and individual customers
- **QR Code Registration**: Each outlet has a unique QR code registration page
- **Visit & Spend Tracking**: Track customer visits, spending, and behavior per outlet
- **Points-Based Loyalty Program**: Configurable earning and redeeming rules
- **Automated Greetings**: Birthday, national day, and custom auto-greetings
- **Customer 360° View**: Complete profile with history and analytics
- **Rich Dashboard**: Demographics, behavior, loyalty, and campaign metrics
- **Excel/CSV Import/Export**: With phone normalization and deduplication
- **Linktree-Style Pages**: Per-outlet social media link pages

### Roles & Permissions
- **Super Admin**: Full system access
- **Group Manager**: Full access across all outlets
- **Marketing/CRM Officer**: Campaigns, greetings, customer management
- **Outlet Manager**: Outlet-scoped access
- **Outlet Staff**: Limited access for visit logging
- **Analytics Read-only**: View-only dashboard access

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL or PostgreSQL
- **Frontend**: Laravel Blade + Bootstrap 5
- **Auth**: Laravel Auth + Spatie Permission
- **Excel**: Maatwebsite Excel
- **Images**: Intervention Image

## Quick Setup

### 1. Clone and Install
```bash
cd hospitality-crm
composer install
npm install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

Configure your `.env` file:
```env
APP_NAME="Hospitality CRM"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospitality_crm
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

### 3. Database Setup
```bash
php artisan migrate --seed
```

### 4. Run Development Server
```bash
php artisan serve
```

## Demo Credentials

After seeding, use these login credentials:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@hospitality.com | password123 |
| Group Manager | manager@hospitality.com | password123 |
| Marketing Officer | marketing@hospitality.com | password123 |
| Outlet Manager | outlet@hospitality.com | password123 |
| Analytics Read-only | analytics@hospitality.com | password123 |

## Outlets (Demo)

After seeding, the following outlets are created:

| Code | Name | Type | City |
|------|------|------|------|
| main-hotel | Grand Bahrain Hotel | hotel | Manama |
| seaside-resort | Seaside Resort & Spa | resort | Sitra |
| downtown-bar | Downtown Bar | bar | Manama |
| italian-bistro | Italian Bistro | restaurant | Muharraq |
| sky-club | Sky Nightclub | club | Manama |

### Outlet QR Code Registration URLs
- `http://localhost/register?outlet=main-hotel`
- `http://localhost/register?outlet=seaside-resort`
- etc.

### Outlet Link Pages
- `http://localhost/o/main-hotel/links`
- `http://localhost/o/seaside-resort/links`
- etc.

## Scheduled Commands

Add to your server's cron:
```bash
* * * * * cd /path/to/hospitality-crm && php artisan schedule:run >> /dev/null 2>&1
```

### Scheduled Jobs
- **Auto-Greetings**: Runs daily at 8 AM to process birthday and fixed-date greetings
- **Campaign Sender**: Processes pending campaign messages
- **Points Expiry**: Monthly check for expired loyalty points

## API Endpoints

### Authentication
```
POST /api/login
POST /api/logout
GET /api/me
```

### Customers
```
GET /api/customers
POST /api/customers
GET /api/customers/{id}
PUT /api/customers/{id}
DELETE /api/customers/{id}
```

### Visits
```
GET /api/visits
POST /api/visits
GET /api/customers/{id}/visits
```

### Loyalty
```
GET /api/loyalty/wallet/{customerId}
POST /api/loyalty/redeem
GET /api/loyalty/rewards
```

### Outlets
```
GET /api/outlets
GET /api/outlets/{id}/social-links
```

### Dashboard
```
GET /api/dashboard/stats
GET /api/dashboard/demographics
```

## Loyalty Program

### Default Earning Rules
- **Base Points**: 10 points per 1 BHD spent
- **Birthday Bonus**: 2x points on birthday visits
- **First Visit Bonus**: 100 welcome points

### Available Rewards (Demo)
- Free Soft Drink (200 points)
- Free Appetizer (500 points)
- Free Night Stay (5000 points)
- 10 BHD Voucher (1000 points)
- 25 BHD Voucher (2500 points)

## Phone Normalization

Phone numbers are normalized to E.164 format:
```json
{
    "country_iso2": "BH",
    "country_dial_code": "+973",
    "national_number": "39123456",
    "e164": "+97339123456"
}
```

Uniqueness is enforced on:
- Email (unique)
- Normalized mobile E.164 (unique)

## Import/Export

### Export Customers
1. Go to Customers list
2. Click "Export" button
3. Select format (CSV/XLSX) and columns
4. Download the file

### Import Customers
1. Go to Settings > Import/Export
2. Upload CSV/XLSX file
3. Map columns to CRM fields
4. Choose mode (Insert-only or Upsert)
5. Review and confirm import

## File Structure

```
hospitality-crm/
├── app/
│   ├── Console/Commands/    # CLI commands
│   ├── Enums/               # Enum definitions
│   ├── Helpers.php          # Global helper functions
│   ├── Http/
│   │   ├── Controllers/     # Web & API controllers
│   │   ├── Requests/        # Form request validators
│   │   └── Middleware/      # Custom middleware
│   ├── Jobs/                # Queueable jobs
│   ├── Models/              # Eloquent models
│   ├── Providers/           # Service providers
│   ├── Services/            # Business logic services
│   └── Traits/              # Reusable traits
├── config/                  # Configuration files
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   └── views/               # Blade templates
├── routes/                  # Route definitions
└── tests/                   # Unit/Feature tests
```

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Database Migrations
```bash
# Create new migration
php artisan make:migration create_customers_table

# Run migrations
php artisan migrate

# Rollback
php artisan migrate:rollback

# Fresh migration
php artisan migrate:fresh --seed
```

## Support

For issues and feature requests, please create an issue in the repository.

## License

MIT License
