# Тесты

- В `phpunit.xml` две сюиты: `Unit` → `tests/Unit` и `Feature` → `tests/Feature`. В `tests/Feature` сейчас
  только `ExampleTest`. Тесты сервисов пишутся в `tests/Unit/Services/<Домен>/`.
- Не запускай у себя тесты.
- Не создавай тесты для jobs, middleware, SharedKernel.
- Окружение тестов — SQLite in-memory (`phpunit.xml`), `Tests\TestCase` подключает `RefreshDatabase`.

## Стиль тестов

- Класс наследует `Tests\TestCase`, пространство имён зеркалит `app/` внутри `tests/Unit/`,
  имя файла — `<Сервис>Test`.
- Имена методов в новых тестах — `test_snake_case`. Позитивный тест — `test_positive_...`,
  негативный — `test_negative_...`. В старых тестах встречается `testCreatePositive` — их не переименовывай.
- Ассерты — `$this->assertSomething(...)`.
- Для исключений: `$this->expectException(HandledException::class)` и `expectExceptionMessage('...')`
  ставь **перед** вызовом, который бросает. Ассерты после него не выполняются. Чтобы проверить, что при
  ошибке данные не изменились, оберни вызов в `try { ... } catch (HandledException) {}` и проверь БД после.
- Текущий пользователь задаётся `Auth::login($user)`.

## Фикстуры — через фабрики моделей

- Данные создаются через `Model::factory()->create([...])`. Фабрики лежат в
  `database/factories/<Домен>/`, у моделей подключён `HasFactory`.
- Тесту нужен новый агрегат — **добавь фабрику** в `database/factories/<Домен>/` (с `protected $model`
  и вложенными фабриками для внешних ключей), а не создавай модели вручную через `->create()` на модели.
- Пример фабрики с зависимостями — `database/factories/Expenses/ExpenseTemplateFactory.php`.

## Дополнительные правила

- В начале метода объявляются переменные со значениями для сервиса, фикстур и ассертов.
- Выравнивание `=` в блоке объявлений — в одну колонку.
- Массивы выравниваются по `=>` в одну колонку.
- Имена переменных должны давать понять, что происходит (`$foreignTemplate`, `$inactiveTemplate`).
- Позитивные и негативные тесты покрывают принадлежность данных пользователю: чужая запись не
  изменяется, не удаляется и не видна.

Пример:

```php
public function test_negative_update_foreign_expense(): void
{
    $service    = $this->app->make(ExpenseService::class);

    $user       = User::factory()->create();
    $expense    = Expense::factory()->create();

    Auth::login($user);

    $this->expectException(HandledException::class);
    $this->expectExceptionMessage('Expense is not found');

    $service->update($expense->id, null, 'Metro', '60');
}
```
