# Suggested commands

User forbids the agent to run: composer, tests (`php artisan test`, phpunit), swagger generation (`l5-swagger:generate`). The user runs them.

Safe for the agent: `php -l <file>` (syntax check), `git status` / `git diff`, Serena tools for navigation.

User-side commands (from `composer.json` scripts / README):
- `composer test` or `php artisan test` — config:clear then tests.
- `composer dev` — serve + queue listener + pail + vite.
- `composer setup` — install, `.env`, key, migrate, npm build.
- `php artisan l5-swagger:generate` — regenerate API docs after annotation changes.

Do not run pint/formatters over the whole project; match the format of the file being edited.
