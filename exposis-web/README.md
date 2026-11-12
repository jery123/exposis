# EXPOSIS

EXPOSIS is a research, experimentation, and innovation community in Côte d’Ivoire. The Next.js public website is in [`../exposis-next`](../exposis-next); this Laravel application remains its data and workflow backend, including member accounts, project submissions, evaluation, moderation, and email notifications. Laravel's original Blade pages remain available as a standalone fallback.

## What the site supports

- Public JSON API for published projects and visitor feasibility submissions.
- Standalone Blade pages explaining EXPOSIS, its focus areas, and published work.
- Public feasibility, prototype, and research submissions at `/soumettre`. Visitors do not need an account; submissions remain private in the review queue.
- Member registration and login. Members can submit completed or in-progress community projects with participant names, a project link, and up to five private images; proposals and images stay private until an administrator publishes them.
- An admin workspace at `/admin` to review feasibility requests, score and evaluate them, and moderate public project submissions.
- A public project catalogue at `/projets`, including participant lists for approved projects.

The site uses the supplied EXPOSIS logo and reflects the founding charter’s focus on applied research, practical learning, useful innovation, collaboration, and knowledge sharing.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm
- SQLite (the default database driver) or another Laravel-supported database

## Local setup

```sh
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate
```

Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env` before creating the administrator. The password must be at least 12 characters.
Set `EXPOSIS_INBOX` to the address that should receive new visitor and member submissions.

```sh
php artisan db:seed
php artisan db:seed --class=AdminSeeder
php artisan serve
```

The default seeder adds five clearly labeled fictional sample projects to the public catalogue. It is safe to run more than once. `AdminSeeder` remains a separate command and requires the credentials above.

Configure a production mail transport in `.env` to send submission and review notifications. The default local mailer writes messages to the log.

## Tests

```sh
php artisan test
```
