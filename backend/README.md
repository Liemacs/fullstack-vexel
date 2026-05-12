# Vexel API

Laravel backend for the Vexel frontend. The local database uses SQLite at `database/database.sqlite`.

## Run

```bash
composer install
php artisan migrate:fresh --seed
php artisan serve
```

The API will be available at `http://localhost:8000/api/v1`.

## Endpoints

- `GET /api/v1/health`
- `GET /api/v1/overview`
- `GET /api/v1/members`
- `GET /api/v1/members/{slug}`
- `GET /api/v1/contracts`
- `GET /api/v1/vehicles`
- `GET /api/v1/map-points`
- `GET /api/v1/timeline`
