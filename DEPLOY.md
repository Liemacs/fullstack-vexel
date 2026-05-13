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
Containerul verifica in productie ca `/var/data` este montat ca disk/volum persistent. Daca lipseste, aplicatia se opreste inainte sa creeze o baza SQLite noua pe storage temporar.
Inainte de fiecare migrare, daca baza exista, se creeaza automat un backup in `/var/data/backups`. Se pastreaza ultimele 10 backup-uri.
La pornire, containerul creeaza automat `.env` cu setarile pentru Laravel, inclusiv `APP_KEY`, `DB_CONNECTION` si `DB_DATABASE`.
Pentru sesiuni stabile, este recomandat sa pastrezi `APP_KEY` ca env var in Render; daca lipseste, containerul genereaza una automat.

## Daca creezi serviciul manual pe Render

Nu seta `docker-compose.yml` ca Dockerfile. Render nu foloseste Docker Compose la deploy.

Seteaza:

- Dockerfile Path: `./Dockerfile`
- Docker Context Directory: `.`

Adauga obligatoriu un Persistent Disk:

- Mount Path: `/var/data`
- `DB_DATABASE`: `/var/data/database.sqlite`
- `REQUIRE_PERSISTENT_SQLITE`: `true`

Daca serviciul porneste fara acest disk, datele SQLite vor fi pierdute la redeploy. Noua configuratie opreste containerul in acest caz ca sa previna o baza goala noua.
