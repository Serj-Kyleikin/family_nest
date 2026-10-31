# Conventions (non-obvious, see `.claude/rules/` for the authoritative list)

- No comments in code; aligned `=`/`=>` columns in declaration blocks and arrays; one method per line in call chains.
- Positive conditions: put the wanted case in `if` or use `throw_unless`; legacy code with `!` is not rewritten.
- Guards: `throw_if` / `throw_unless(cond, HandledException::class, 'message', status)`; each argument on its own line; messages are plain strings (no `___()` helper exists), do not translate existing ones.
- Repository: extends `AbstractRepository` (create, insert, update(filters,data), updateWhereIn, delete(filters), getWhere, firstWhere); model via promoted `protected Model $model`; no typed `$model` in the abstract class (PHP forbids narrowing in children). Specific queries are methods of the concrete repo (e.g. `ExpenseTemplateRepository::firstForUser`).
- Models use `$fillable`; check `$timestamps` / `SoftDeletes` per model (`Expense` soft-deletes; templates and template groups have no timestamps).
- Migrations: anonymous class, `down()` always, FK columns as plain `bigInteger`/`unsignedBigInteger` without constraints, new column = new migration.
- Factories: `database/factories/<Domain>/<Model>Factory.php` with `protected $model`, nested factories for FKs; tests use `Model::factory()->create([...])`, `Auth::login($user)`.
- Tests: `tests/Unit/Services/<Domain>/<Service>Test.php`; new names `test_positive_*` / `test_negative_*` (older tests are camelCase, keep them); set `expectException` before the throwing call; no tests for jobs/middleware/SharedKernel.
- Swagger one-line properties: `new OA\Property(property: 'x', type: 'string', example: '...'),`; nested object properties stay multi-line; `operationId` camelCase `<domain><Action>` unique project-wide.
- Controller shape: cast request values, call service in `try`, `catch` -> `return $this->error($exception)`; Family/Auth catch `HandledException`, Chat/Expenses catch `\Exception`.
