# WBL System

Laravel 11 MVC Application for Work-Based Learning Management System.

## Installation

1. Install dependencies:
```bash
composer install
npm install
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Configure database in `.env` file

5. Run migrations and seeders:
```bash
php artisan migrate --seed
```

6. Start development server:
```bash
php artisan serve
npm run dev
```

## Default Credentials

- **Admin**: admin@wbl.com / password
- **Role**: admin

## Project Structure

- Models: `app/Models`
- Controllers: `app/Http/Controllers`
- Views: `resources/views`
- Services: `app/Services`
- Repositories: `app/Repositories`
- Migrations: `database/migrations`
- Seeders: `database/seeders`

# wbl-ftkma
