# Deploy pe Render

Proiectul poate rula ca un singur serviciu Docker:

- Laravel API + dashboard
- frontend Vite build-uit static in `public/`

## Local cu Docker

```bash
docker compose up --build
```

Cu Dockerfile-ul din root, pe Render poti lasa:

- Dockerfile Path: `./Dockerfile`
- Docker Build Context Directory: `.`

## Render

Poti folosi `render.yaml` din root pentru Blueprint deploy.

Setari importante dupa creare:

- Seteaza `APP_URL` la URL-ul public Render.
- `API_BASE_URL` poate ramane `/api/v1`, fiindca frontend-ul si backend-ul ruleaza in acelasi container.

Backend-ul foloseste SQLite pe disk persistent la `/var/data/database.sqlite`.
Containerul ruleaza automat `php artisan migrate --force` la pornire.

Nu ruleaza seed la pornire, ca sa nu stearga datele create din dashboard.

## Daca creezi serviciul manual pe Render

Nu seta `docker-compose.yml` ca Dockerfile. Render nu foloseste Docker Compose la deploy.

Seteaza:

- Dockerfile Path: `./Dockerfile`
- Docker Context Directory: `.`
