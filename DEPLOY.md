# Deploy pe Railway

Proiectul ruleaza ca un singur serviciu Docker:

- Laravel API + dashboard
- frontend Vite build-uit static in `public/`
- baza de date intr-un serviciu separat PostgreSQL

## Railway

1. Creeaza un serviciu din repository pentru aplicatie.
2. Creeaza un serviciu separat `PostgreSQL` in acelasi project Railway.
3. In serviciul aplicatiei, adauga variabila:

```txt
DATABASE_URL=${{Postgres.DATABASE_URL}}
```

Numele `Postgres` trebuie sa fie exact numele serviciului PostgreSQL din Railway. Daca Railway ti-a creat DB-ul cu alt nume, foloseste acel nume in loc de `Postgres`.

Variabile recomandate pentru aplicatie:

```txt
APP_NAME=Vexel API
APP_ENV=production
APP_DEBUG=false
APP_URL=https://vexel-production.up.railway.app
APP_KEY=base64:...
DATABASE_URL=${{Postgres.DATABASE_URL}}
API_BASE_URL=/api/v1
LOG_CHANNEL=stderr
```

Nu seta `DB_CONNECTION=sqlite` pe Railway. Daca exista `DATABASE_URL`, containerul seteaza automat Laravel pe `pgsql`.

Containerul ruleaza automat:

```bash
php artisan migrate --force
```

Nu ruleaza `seed`, `migrate:fresh` sau alte comenzi care sterg datele.

## Date persistente

Baza de date este persistenta in serviciul PostgreSQL Railway, nu in containerul aplicatiei. Redeploy-ul aplicatiei nu sterge datele din PostgreSQL.

Atentie: fisierele uploadate local in container pot fi efemere pe Railway. Pentru imagini persistente pe termen lung, foloseste un Railway Volume montat la `/var/data` sau un storage extern.

## Local cu Docker Compose

```bash
docker compose up --build
```

Configuratia locala foloseste tot PostgreSQL separat, cu volum Docker `postgres-data`.

## Config din repository

Repo-ul contine `railway.json`, care spune Railway sa foloseasca Dockerfile-ul din root.

Repo-ul nu mai contine variabile SQLite implicite in Dockerfile si nu mai contine `render.yaml`. Variabilele vechi afisate in Railway trebuie sterse manual din dashboard daca au fost deja importate in serviciu.
