# Task completion checklist

Agent does not run tests, composer or swagger generation; state that in the final report.

1. Syntax: `php -l` on every touched PHP file.
2. Callers: `find_referencing_symbols` on every changed signature (Util `isExists`, repository methods, service methods) and confirm all callers were updated.
3. Annotations: if a route, request rule or response changed, update the controller `OA` attributes (and mention that `l5-swagger:generate` is for the user to run).
4. Schema: a new column used in code needs a new migration and `$fillable` entry; check the existing migrations first.
5. Ownership: any new read/update/delete of user data filters by owner.
6. Tests: add or update a service test (and a factory if a new aggregate is needed); do not run them.
7. No commit, push or branch switch unless asked; do not edit `.claude/rules/` unless the user asks.
8. Report what was verified (lint, Serena references) separately from what was not (tests, runtime).
