# Tech stack

- PHP `^8.3`, Laravel 13 (`^13.8`); exact versions only in `composer.json`; `composer.lock` is committed.
- Auth: `laravel/sanctum` (guard via `auth:sanctum`, `HasApiTokens` on `User`). Roles: `spatie/laravel-permission` (`client` assigned on sign-up, `admin`; seeded by `RoleTableSeeder` with guard `web`).
- API docs: `darkaonline/l5-swagger` (config `config/l5-swagger.php`), attributes `OpenApi\Attributes as OA`.
- Tests: PHPUnit 12, SQLite in-memory (`phpunit.xml`), `Tests\TestCase` uses `RefreshDatabase`; suites `Unit` and `Feature`.
- Dev: pint (no config file), pail, pao, collision, mockery, faker.
- Not installed: Redis/predis, Passport, HTTP clients, mail/SMS packages. Any new dependency needs user approval.
- Frontend: Vite + thin Blade shell in `resources/`, untouched.
- Serena language server: `php` (`.serena/project.yml`); `.serena/cache` is git-ignored via `.serena/.gitignore`.
