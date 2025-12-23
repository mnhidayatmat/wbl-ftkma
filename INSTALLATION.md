# WBL System - Installation Guide

## Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 18.x and npm
- MySQL/MariaDB or SQLite

## Installation Steps

### 1. Install PHP Dependencies

```bash
composer install
```

### 2. Install Node Dependencies

```bash
npm install
```

### 3. Environment Configuration

Copy the environment file:

```bash
cp .env.example .env
```

Edit `.env` file and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wbl_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

This will create:
- Default admin user (admin@wbl.com / password)
- 5 WBL groups
- 5 companies
- 10 sample students

### 6. Start Development Servers

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```

### 7. Access the Application

Open your browser and navigate to:
- http://localhost:8000

## Default Login Credentials

- **Admin**: admin@wbl.com / password
- **Lecturer**: lecturer@wbl.com / password
- **Industry**: industry@wbl.com / password
- **Student**: student@wbl.com / password

## Project Structure

```
wbl-ftkma/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── StudentController.php
│   │   │   ├── GroupController.php
│   │   │   ├── CompanyController.php
│   │   │   └── DashboardController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── WblGroup.php
│   │   └── Company.php
│   ├── Services/
│   └── Repositories/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── students/
│   │   ├── groups/
│   │   └── companies/
│   ├── css/
│   └── js/
└── routes/
    ├── web.php
    └── api.php
```

## Features

- ✅ Clean MVC Architecture
- ✅ RESTful Resource Routes
- ✅ Authentication with Role-Based Access (admin, lecturer, industry, student)
- ✅ Student Management (CRUD)
- ✅ Group Management (CRUD)
- ✅ Company Management (CRUD)
- ✅ TailwindCSS Styling
- ✅ Responsive Sidebar Navigation
- ✅ Form Validation
- ✅ Database Relationships
- ✅ Factories and Seeders

## Routes

### Public Routes
- `/` - Redirects to dashboard
- `/login` - Login page
- `/register` - Registration page

### Protected Routes (Requires Authentication)
- `/dashboard` - Dashboard
- `/students` - Student management (resource routes)
- `/groups` - Group management (resource routes)
- `/companies` - Company management (resource routes)

## Building for Production

```bash
npm run build
```

This will compile and minify your assets for production use.

