# FamilyNest core

API-only Laravel backend (no frontend). Rules live in `.claude/rules/` (imported by CLAUDE.md) and win over memories; code wins over both.

Domains (`app/Services/<Domain>/`): Auth, Family, Chat, Expenses. Each domain: service(s), `Repositories/`, `Util/*RulesUtil` (static guards), optional `Handler/`, `DTO/`, `Swagger/` (Chat only).

Request path: `routes/api.php` -> `app/Providers/Routes/<Domain>/<Domain>RoutesProvider::register()` (called via `app(...)->register()`; all but Auth inside `auth:sanctum` group) -> FormRequest -> Controller (`app/Http/Controllers/Api/<Domain>`) -> Service -> Repository -> Model.

Invariants:
- No API versioning, no repository interfaces/bindings (`AppServiceProvider` empty): services inject concrete repositories, so references to a concrete class are the real usage points.
- Current user is taken inside services via `Auth::id()` / `Auth::user()`, not passed from controllers.
- Errors: `HandledException` (message, int HTTP status) caught in controller and rendered by `Controller::error()` as `{status:false,message}`. Chat/Family Util classes still throw plain `Exception`.
- Success envelope: `{status:true, data?, message?}`.
- Ownership: every user-data lookup/update/delete must filter by owner (`user_id` or owning parent); `*RulesUtil::isExists` takes `$userId`.
- `app/SharedKernel/` holds only `Repository/` (AbstractRepository + Create/Update/Delete contracts) and `Traits/Swagger/`; changes need user approval.
- Swagger: PHP attributes in controllers; Chat response schemas are legacy docblock classes in `app/Services/Chat/Swagger/`.
- Money: `expenses.amount` decimal(12,2), passed as string.

More: stack and versions `mem:tech_stack`; commands and what must not be run `mem:suggested_commands`; code and test conventions `mem:conventions`; done-checklist `mem:task_completion`.
