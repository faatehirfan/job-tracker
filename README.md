# job-tracker

A small Laravel 13 app for tracking my own job hunt: companies, the roles I applied for, and where
each application stands. Built in Week 3 of the Laravel Developer Residency.

## Run it locally

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan test
```

With Laravel Herd, the app is served at `http://job-tracker.test` from Herd's parked folder.