# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Important Rules

**NEVER remove or delete any existing database data or tables.** When making database changes:
- Only create new migrations that ADD columns or tables
- Do NOT use `migrate:fresh` or `migrate:refresh` commands
- Do NOT drop tables or columns with existing data
- Always preserve existing production data

## Project Overview

WBL (Work-Based Learning) Management System - A Laravel 11 application for managing academic assessments across multiple modules (PPE, FYP, IP, OSH, LI) for UMPSA university. Handles students, lecturers, industry coaches, and administrators.

## Common Commands

```bash
# Development
php artisan serve              # Start Laravel server (localhost:8000)
npm run dev                    # Vite dev server with hot reload

# Database
php artisan migrate --seed     # Run migrations with seeders
php artisan migrate:refresh --seed  # Reset and re-seed database
php artisan tinker             # Interactive shell

# Testing
php artisan test               # Run all tests
php artisan test tests/Feature/SomeTest.php  # Single test file
php artisan test --filter=test_method_name   # Single test method

# Code Quality
./vendor/bin/pint              # Format code with Laravel Pint

# Cache
php artisan cache:clear && php artisan config:clear && php artisan view:clear
```

## Default Credentials

- Admin: `admin@wbl.com` / `password`

## Architecture

### Module Organization

Controllers and models are organized by academic module under `app/Http/Controllers/Academic/` and `app/Models/`:
- **PPE** - Professional Practice Evaluation (40% AT / 60% IC marks)
- **FYP** - Final Year Project
- **OSH** - Occupational Safety & Health
- **IP** - Industrial Project
- **LI** - Learning Integration

Routes are split across files: `routes/web.php`, `routes/academic.php`, `routes/ppe.php`, `routes/fyp.php`, etc.

### Role System

Dual role system for backward compatibility:
1. Legacy: `users.role` column (string)
2. New: `user_roles` pivot table with `roles` table

Session-based role switching via `session('active_role')` allows users with multiple roles to switch contexts.

```php
// Role checking methods on User model
$user->hasRole('admin')           // Check via roles table
$user->hasAnyRole(['lecturer', 'at'])
$user->isAdmin(), isLecturer(), isIndustry(), isStudent()
$user->getActiveRole()            // Get session-based active role
$user->isActingAs('lecturer')     // Check active role
```

### Permission System

Granular permissions stored in `permissions` and `role_permissions` tables. Use `PermissionHelper` for checks:
```php
PermissionHelper::canAccess($module, $action, $accessLevel)
PermissionHelper::can($module, $action)
```

Modules: `ppe`, `fyp`, `ip`, `osh`, `li`, `students`, `companies`, `reports`
Actions: `view`, `evaluate`, `create`, `update`, `delete`, `export`, `finalise`, `moderate`

### Assessment Structure

Hierarchical: Assessment -> Components -> CLOs -> Rubrics
- `assessments` - Assessment definitions
- `assessment_components` - Component breakdown
- `assessment_clos` - Course Learning Outcome mappings
- `assessment_rubrics` - Rubric templates
- Student marks tracked at component and rubric levels

Each module has its own marks tables (e.g., `ppe_student_at_marks`, `ppe_student_ic_marks`).

## Key Relationships

```
User -> Student (hasOne)
User -> assignedStudents (hasMany via ic_id) - IC's students
User -> assignedStudentsAsAt (hasMany via at_id) - AT's students
User -> Company (belongsTo) - for IC users
User <-> Roles (belongsToMany via user_roles)
```

## Styling

Uses TailwindCSS with UMPSA corporate colors defined in `tailwind.config.js`:
- Primary: `umpsa-primary` (#003A6C)
- Secondary: `umpsa-secondary` (#0084C5)
- Accent: `umpsa-accent` (#00AEEF)

## Key Packages

- `maatwebsite/excel` - Excel import/export
- `barryvdh/laravel-dompdf` - PDF generation
- `laravel/breeze` - Authentication scaffolding
