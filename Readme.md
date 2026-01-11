# Tasks Microservice

## Quick start (from the project root)

1. Unzip and change to project directory (if needed)

```bash
cd tasks-microservice
```

2. Build the images

```bash
docker compose build
```

3. Install PHP dependencies (if `vendor/` is not included in the archive)

```bash
docker compose run --rm php composer install
```

4. Start services

```bash
docker compose up -d
```

5. Verify and open

```bash
docker compose ps
docker compose logs -f php
```

## Common tasks & troubleshooting

- Shell into the php container:

```bash
docker compose exec php bash
```

- Re-run DB init (init.sql runs only the first time the volume is created):

```bash
docker compose down -v
docker compose up -d
```

- If port 8000 is already in use, change `ports` in `docker-compose.yml`.

- Volume overlay note: the compose file mounts the host project into the container (`.:/app`). If you build Composer dependencies inside the image but mount the host afterward, the host mount can hide files created during build. Running `docker compose run --rm php composer install` writes `vendor/` on the host and avoids this problem.

## What to include in the ZIP

Recommended to exclude host and build artifacts to keep the archive small:

- Exclude: `vendor/`, `pgdata/`, `.env`, `.git/`, `.vscode/`
- Include: `composer.json`, `composer.lock`, `Dockerfile`, `docker-compose.yml`, `init.sql`, `src/`, `public/`, `README.md`

## Notes

- The php service is started with the built-in PHP server and exposed on port 8000.
- The php container name is `tasks-microservice` (set in `docker-compose.yml`).
- Database is Postgres 15 and initialized with `init.sql` on first startup.
