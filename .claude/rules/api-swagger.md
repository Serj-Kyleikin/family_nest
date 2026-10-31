# API и Swagger

- Генерацию swagger не запускать.

## Формат ответа

- Успех: `{"status": true, "data": ..., "message": "..."}`.
- `message` обязателен в ответах создания, обновления и удаления всегда. В схеме ответа такого
  эндпоинта: `required: ['status', 'message']`.
- `data` — для чтения, списков и объектов. Создание и обновление возвращают в `data` актуальную
  сущность (для обновления — после обновления).
- Ошибка: `{"status": false, "message": "..."}` — формирует `Controller::error()`.
- Форму ответа нового эндпоинта копируй с соседних эндпоинтов того же домена. Не изобретай свой конверт.
- Статусы успеха: `200` (чтение, обновление, удаление), `201` (создание) — константами `Response::HTTP_*`.
- Деньги (`amount`) передаются и принимаются строкой с `decimal:0,2`; не приводи их к `int` и `float`.

## Где лежит описание

Два способа. Способ определяется контроллером, в одном контроллере не смешивай оба.

- **`app/Swagger`** — описание операции в отдельном классе, в контроллере только ссылка на него.
  Так описаны контроллеры домена Family: `FamilyGroupController`, `FamilyMemberController`,
  `FamilyLinkController`, `FamilyLinkRequestController`, а также `UserController` домена User.
  Контроллеры других доменов на этот способ не переводи без моей просьбы.
- **Атрибуты `OA` над методом контроллера** — все остальные контроллеры (`use OpenApi\Attributes as OA;`).
  Аннотации везде атрибуты PHP 8, не докблоки.

Что где лежит независимо от способа:

- **Тело запроса** — `OA\RequestBody` с `OA\JsonContent` в описании операции. В FormRequest аннотаций нет.
- **Общая информация API** (`OA\Info`, `OA\Server`, `OA\SecurityScheme` с именем `bearerAuth`) — на базовом `Controller`.
- **Схема сущности**, которая переиспользуется (`FamilyGroup`, `FamilyMember`, `UserName`, `ChatDiscussion`) — атрибутом
  `#[OA\Schema]` на самой модели. Операции ссылаются на неё через `ref`.
- **Схемы ответов Chat** лежат в `app/Services/Chat/Swagger/` (`Resources`, `Virtuals/Models`),
  там докблоки `@OA\Schema` — это legacy.
- Выносишь схему в новое место или переводишь контроллер на другой способ — спроси меня.

## `app/Swagger`

### Структура

- `app/Swagger/` зеркалит `app/Http/Controllers/`: путь и имя папки повторяют контроллер.
  `App\Http\Controllers\Api\Family\FamilyGroupController` →
  `app/Swagger/Api/Family/FamilyGroupController/` (namespace `App\Swagger\Api\Family\FamilyGroupController`).
- Внутри папки один класс на каждый метод контроллера, имя класса — имя метода в PascalCase:
  `create` → `Create`, `show` → `Show`, `update` → `Update`, `destroy` → `Destroy`.
  Один класс — один файл.

### Класс описания

- Наследует атрибут `OA` по HTTP-методу: `OA\Get`, `OA\Post`, `OA\Put`, `OA\Delete`.
- Помечен `#[Attribute(Attribute::TARGET_METHOD)]`.
- Конструктор без параметров, внутри вызывает `parent::__construct(...)` со всеми параметрами
  операции: `path`, `operationId`, `tags`, `summary`, `description`, `security`, `parameters`,
  `requestBody`, `responses`.
- Без комментариев в коде.

### Подключение в контроллере

- Импорт папки контроллера с алиасом: `use App\Swagger\Api\Family\FamilyGroupController as Swagger;`
- Над методом одна строка: `#[Swagger\Show]`.
- `use OpenApi\Attributes as OA;` из такого контроллера убирается.

### Что делать при изменениях

- Добавил или изменил метод контроллера — создай или поправь его класс в `app/Swagger`.
- Изменил `rules()` запроса — поправь `requestBody` в классе описания.
- Изменил поля модели — поправь `#[OA\Schema]` на модели.

## Правила FormRequest

- Расхождение между `rules()` и описанием тела запроса недопустимо: поменял правило —
  поменяй `OA\JsonContent` (тип, `nullable`, `required`), и наоборот.

## Соглашения аннотаций

- `operationId` обязателен и уникален в пределах проекта. Формат — camelCase `<домен><Действие>`:
  `expenseList`, `expenseTemplateGroupStore`, `chatDiscussionDelete`. Перед тем как придумывать новый,
  проверь, что такого нет.
- `tags: ['...']` — переиспользуй существующий тег контроллера.
- Защищённые эндпоинты объявляют `security: [['bearerAuth' => []]]`.
- Ссылка на схему — по имени: `ref: '#/components/schemas/ИмяСхемы'`.
- Документируй реальные ответы: `401` для защищённых, `404` если запись ищется по id, `422` для
  валидируемых эндпоинтов.
- Тексты (`summary`, `description`, описания ответов, примеры) — на русском. Пример реального ответа
  фреймворка (`Unauthenticated.`) не переводится.

## Оформление

Одно `new OA\Property(...)` — одна строка. Вложенные объекты с дочерними свойствами остаются многострочными.
Последовательность, отступы и выравнивание сохраняй как в примерах ниже.

### Схема модели и ресурса

- Порядок параметров `property`, `type`, `example`, затем остальные (`nullable`, `format`, `ref`).
- Значения выравниваются колонками для читаемости.

```php
#[OA\Schema(
    schema: 'FamilyGroup',
    properties: [
        new OA\Property(property: 'id',         type: 'integer', example: 1),
        new OA\Property(property: 'name',       type: 'string',  example: 'Ивановы',             nullable: true),
        new OA\Property(property: 'created_at', type: 'string',  example: '2026-06-14 12:00:00', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string',  example: '2026-06-14 12:00:00', format: 'date-time'),
    ]
)]
```

### Класс описания контроллера

- Группы параметров идут в таком порядке и отделены пустой строкой: `path`, `operationId`, `tags`;
  `summary`, `description`; `security`; `requestBody`; `responses`.
- Ключи в каждой группе выровнены по одной колонке значений.
- Вложенные `OA\Response`, `OA\RequestBody`, `OA\JsonContent` выровнены так же: ключи в колонку, значения
  вложенных объектов начинаются с колонки значений, закрывающая скобка стоит под началом значения.
- Многострочные `OA\Response` отделены пустой строкой, однострочные (`401`, `404`, `422`) идут подряд в конце.
- Свойства `OA\Property` в одном блоке выровнены: `property`, `type` и третий параметр стоят в своих колонках.

```php
class Update extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group',
            operationId:      'familyGroupUpdate',
            tags:             ['Family Group'],

            summary:      'Изменить семейную группу',
            description:  'Изменяет название семейной группы текущего пользователя и возвращает обновлённую группу.',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                type:       'object',
                                required:   ['name'],
                                properties: [
                                                new OA\Property(property: 'name', type: 'string', example: 'Ивановы'),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Группа успешно обновлена',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',    type: 'object',  ref: '#/components/schemas/FamilyGroup'),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Группа успешно обновлена.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
```
