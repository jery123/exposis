# EXPOSIS

EXPOSIS is a research, experimentation, and innovation community in Côte d’Ivoire. This Laravel application serves the public website and member-facing screens with Blade, following the content and visual direction of the standalone Next.js preview in [`../exposis-next`](../exposis-next). Laravel continues to manage authentication, project submissions, evaluation, moderation, persistence, uploads, and email notifications.

## What the site supports

- Public JSON API for published projects and visitor feasibility submissions.
- Blade pages for the public website, authentication, member projects, and profile.
- Public feasibility, prototype, and research submissions at `/soumettre`. Visitors do not need an account; submissions remain private in the review queue.
- Member registration and login. Members can submit completed or in-progress community projects with participant names, a project link, and up to five private images; proposals and images stay private until an administrator publishes them.
- An admin workspace at `/admin` to review feasibility requests, score and evaluate them, and moderate public project submissions.
- A public project catalogue at `/projets`, including participant lists for approved projects.
- Approved reports stay in private storage. Visitors can read a page-image preview limited to the first half; any authenticated member can open the complete report or download the original once the project and report are published and approved.

The site uses the supplied EXPOSIS logo and reflects the founding charter’s focus on applied research, practical learning, useful innovation, collaboration, and knowledge sharing.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm
- SQLite (the default database driver) or another Laravel-supported database
- PHP DOM/XML, ZipArchive, and GD extensions plus Poppler utilities (`pdfinfo`, `pdftoppm`) for report previews. The Docker image installs these. DOCX previews render visible document text and do not include the original layout or embedded images.

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
