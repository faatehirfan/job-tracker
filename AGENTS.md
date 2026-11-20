# AGENTS.md

Instructions for AI coding agents working in this repository. Written by hand.

## What this is

`job-tracker`: a Laravel 13 web app that tracks my job hunt. Companies have many job applications;
each application has a status (wishlist, applied, interviewing, offer, rejected). Plain Blade views
styled by simple.css from a CDN. No starter kit, no front-end build step, no JavaScript.

## Stack

- PHP 8.5, Laravel 13, SQLite (`database/database.sqlite`, not committed). Served locally by Laravel
  Herd at `http://job-tracker.test`.
- Tests: Pest with `RefreshDatabase`, against an in-memory SQLite database (see `phpunit.xml`).
- Style: Pint (Laravel preset). Static analysis: Larastan, level 5 (`phpstan.neon`).

## Commands

- Install: `composer install`, then `cp .env.example .env` and `php artisan key:generate`
- Database: `php artisan migrate`, then `php artisan db:seed` for sample data
- Test: `php artisan test`
- Lint: `composer lint` (Pint `--test`, then PHPStan with `--memory-limit=1G`)
- Fix style: `composer fix`

CI (`.github/workflows/ci.yml`) runs Pint, PHPStan and the tests. A change is done when all three pass.

## Layout

- `app/Enums/ApplicationStatus.php`: the five statuses, stored as strings, with `label()`.
- `app/Models/`: `Company` (hasMany `jobApplications`) and `JobApplication` (belongsTo `company`).
- `app/Http/Controllers/`: `JobApplicationController` (all 7 resource actions) and `CompanyController`
  (index, create, store, show). Validation is inline with `$request->validate([...])`.
- `routes/web.php`: `/` redirects to `applications.index`; two `Route::resource` lines.
- `resources/views/`: `layouts/app.blade.php` plus `applications/` and `companies/` views. Views use
  `@extends('layouts.app')` and `@section('content')`. The application form is the `_form` partial.
- `database/migrations/`, `database/factories/`, `database/seeders/DatabaseSeeder.php`.
- `tests/Feature/`: one file per controller, plus `JobApplicationValidationTest.php`.

## Conventions

- Follow the code that `php artisan make:*` generates (no `declare(strict_types=1)` in this repo).
- Models use the `$fillable` property and the `casts()` method. Statuses are always
  `ApplicationStatus` cases in PHP code, never bare strings.
- Every list page eager-loads what its view uses (`with('company')`). Lazy loading throws outside
  production (`Model::preventLazyLoading()` in `AppServiceProvider`).
- Money is whole ringgit per month, stored as unsigned integers.
- Every controller action has a feature test. Test names describe behaviour.
- English everywhere: code, comments, commit messages, PR descriptions.

## Never do this

- Never edit a migration that has already run or been pushed. Write a new migration instead.
- Never run `php artisan migrate:fresh`, `migrate:reset`, `migrate:rollback`, `db:wipe`,
  `git reset --hard` or `rm -rf`. They destroy data or work. Ask me, and I will run them myself.
- Never read, print or edit `.env`. It holds the app key.
- Never edit `vendor/`, `composer.lock` by hand, or `.github/workflows/ci.yml` (the cohort template).

## How to help me

I am learning Laravel (Phase 1 of the residency). Explain and point me to the file and line; do not
write or edit the code for me, because I type it myself. In plan mode, list the files you would touch
and why, and stop there.