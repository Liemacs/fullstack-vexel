# Deploy pe Render

Proiectul are doua servicii Docker:

- `backend` - Laravel API + dashboard
- `web` - frontend Vite servit cu Nginx

## Local cu Docker

```bash
docker compose up --build
```

URL-uri locale:

- Frontend: `http://localhost:5175`
- Backend: `http://localhost:8000`
- API health: `http://localhost:8000/api/v1/health`
- Dashboard: `http://localhost:8000/dashboard`

## Render

Poti folosi `render.yaml` din root pentru Blueprint deploy.

Setari importante dupa creare:

- Pentru `vexel-backend`, seteaza `APP_URL` la URL-ul public al backend-ului Render.
- Pentru `vexel-web`, seteaza `API_BASE_URL` la:

```text
https://URL-BACKEND-RENDER/api/v1
```

Backend-ul foloseste SQLite pe disk persistent la `/var/data/database.sqlite`.
Containerul ruleaza automat `php artisan migrate --force` la pornire.

Nu ruleaza seed la pornire, ca sa nu stearga datele create din dashboard.
