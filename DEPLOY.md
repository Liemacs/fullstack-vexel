# Deploy pe Railway

Proiectul este configurat fara Docker. Railway foloseste `railway.json` si scripturile din `scripts/`.

## Cum ruleaza

Build:

```bash
./scripts/railway-build.sh
```

Scriptul instaleaza dependintele Laravel, construieste frontend-ul Vite si copiaza `web/dist` in `backend/public`.

Start:

```bash
./scripts/railway-start.sh
```

Scriptul seteaza Laravel pe SQLite, creeaza automat fisierul DB daca lipseste, ruleaza migrarile si porneste serverul pe portul primit de la Railway.

## Variabile Railway

Pentru aplicatie lasa:

```txt
APP_ENV=production
APP_DEBUG=false
API_BASE_URL=/api/v1
LOG_CHANNEL=stderr
DB_CONNECTION=sqlite
DB_DATABASE=/var/data/database.sqlite
```

Recomandat:

```txt
APP_KEY=base64:...
```

Daca nu setezi `APP_KEY`, scriptul genereaza una la pornire, dar sesiunile se pot invalida la redeploy.

## Persistenta SQLite

Pentru ca datele sa nu dispara la redeploy, pastreaza Railway Volume-ul atasat la aplicatie:

```txt
Mount Path: /var/data
DB_DATABASE=/var/data/database.sqlite
```

Volume-ul poate ramane numit `vexel-vexel-sqlite`; numele lui din Railway nu conteaza. Important este mount path-ul `/var/data`.

## Ce a fost scos

Au fost eliminate fisierele Docker:

- `Dockerfile`
- `backend/Dockerfile`
- `docker-compose.yml`
- `.dockerignore`
- scripturile `docker/`

Railway nu mai trebuie setat pe Dockerfile builder.
