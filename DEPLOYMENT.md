# Production deployment

This repository includes a Docker Compose deployment for a Linux VPS with a
public IPv4/IPv6 address. It runs the Next.js website, Laravel/PHP-FPM backend,
Nginx, a persistent PostgreSQL database, a Laravel queue worker, and Caddy for
automatic HTTPS. Only ports 80 and 443 are published; the database and PHP-FPM
are private to the Docker networks.

Before building from a fresh clone, ensure the Next.js source is present in
`exposis-next`. The parent Git repository records that directory as a submodule
but currently has no `.gitmodules` mapping, so a fresh clone may leave it empty.

## Requirements

- A Linux VPS with Docker Engine and the Docker Compose plugin.
- A domain name whose DNS `A` record (and, if used, `AAAA` record) points to the
  VPS. Allow inbound TCP ports 80 and 443; UDP 443 is optional for HTTP/3.
- An SMTP account if the site must deliver email notifications.

## Configure and start

From the repository root on the VPS:

```sh
cp .env.production.example .env.production
```

Edit `.env.production` and set the real domain, a unique administrator email,
and strong secrets. Generate a Laravel-compatible application key with
`printf 'base64:%s\n' "$(openssl rand -base64 32)"` and a database password
with `openssl rand -hex 32`. Set `ADMIN_PASSWORD` to a unique password of at
least 12 characters. Do not commit `.env.production` or share it.

Set `APP_DOMAIN` to just the hostname (no scheme). Point DNS at the server
before starting Caddy so it can issue a TLS certificate. Set `MAIL_MAILER`,
`MAIL_HOST`, `MAIL_PORT`, `MAIL_SCHEME`, `MAIL_USERNAME`, `MAIL_PASSWORD`, and
`MAIL_FROM_ADDRESS` to the values from your mail provider. Until SMTP is
configured, the example uses Laravel's `log` mailer and notifications will not
be delivered.

Build and start the services:

```sh
docker compose --env-file .env.production up -d --build
docker compose --env-file .env.production ps
```

Apply the database migrations and create the initial administrator:

```sh
docker compose --env-file .env.production exec backend php artisan migrate --force
docker compose --env-file .env.production exec backend php artisan db:seed --class=AdminSeeder --force
```

Do not run the general `db:seed` command on production; it adds sample projects.
Visit `https://APP_DOMAIN` after replacing that text with the configured domain.

## Updates and data safety

Pull the intended code revision, then rebuild and restart:

```sh
docker compose --env-file .env.production up -d --build
docker compose --env-file .env.production exec backend php artisan migrate --force
```

Back up the PostgreSQL database **and** the `laravel_storage` volume before
upgrading. These contain the site's records and uploaded member files. The
`postgres_data`, `laravel_storage`, and Caddy volumes persist across container
rebuilds; removing volumes will permanently delete their data. Configure
off-server backups and test restores before accepting real submissions.

Check service health and logs with:

```sh
docker compose --env-file .env.production ps
docker compose --env-file .env.production logs --tail=100
```

This setup prepares the application for VPS deployment; it does not provision a
server, register/configure DNS, or create SMTP credentials. Those require
accounts and values from the operator.
