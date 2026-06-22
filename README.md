# Invoice Management

Тестове full-stack завдання для роботи з інвойсами.

Проєкт організований як monorepo: Laravel REST API, Nuxt frontend і спільна Docker-інфраструктура.

## Технології

- **Frontend:** Nuxt 4, Vue 3.5, TypeScript, Tailwind CSS 4, vee-validate, Zod
- **Backend:** PHP 8.2+, Laravel 12, REST JSON API
- **Database:** MySQL 8.4
- **Infrastructure:** Docker Compose, Nginx, PHP-FPM

## Структура проєкту

```text
invoice-management/
├── backend/                 # Laravel REST API
├── frontend/                # Nuxt frontend application
├── infra/
│   └── nginx/               # Nginx configuration for Laravel
├── docker-compose.yml
├── .env.example
├── Makefile
└── README.md
```

## Запуск проєкту

### 1. Створити кореневий `.env`

```bash
cp .env.example .env
```

### 2. Запустити всі сервіси

```bash
docker compose up --build
```

Після успішного запуску будуть доступні:

- Frontend: `http://localhost:3000/invoices`
- Backend API: `http://localhost:8081/api/invoices`
- MySQL: `localhost:3307`

Під час старту контейнер `backend-php` автоматично:

- генерує Laravel application key;
- виконує міграції;
- створює тестові інвойси через seed.

## API

| Метод | Endpoint | Опис |
|---|---|---|
| `GET` | `/api/invoices` | Отримати список інвойсів із pagination та сортуванням за `created_at desc` |
| `GET` | `/api/invoices/{id}` | Отримати один інвойс |
| `POST` | `/api/invoices` | Створити інвойс |
| `PUT` | `/api/invoices/{id}` | Оновити фінансові поля інвойсу зі статусом `pending` |

### Приклад створення інвойсу

```json
{
  "number": "INV-2026-0010",
  "supplier_name": "ТОВ «Приклад»",
  "supplier_tax_id": "12345678",
  "net_amount": 1000.00,
  "vat_amount": 200.00,
  "gross_amount": 1200.00,
  "currency": "UAH",
  "status": "pending",
  "issue_date": "2026-06-21",
  "due_date": "2026-07-21"
}
```

## Бізнес-правила та валідація

Backend є основним джерелом істини для валідації даних.

Перевіряються такі правила:

1. `number` є обов’язковим та унікальним.
2. `net_amount` має бути більшим за `0`.
3. `vat_amount` не може бути від’ємним.
4. `gross_amount` має дорівнювати `net_amount + vat_amount`.
5. `due_date` не може бути раніше за `issue_date`.
6. Редагування доступне лише для інвойсу зі статусом `pending`.
7. Дозволені статуси: `pending`, `approved`, `rejected`.
8. Дозволені валюти: `UAH`, `EUR`, `USD`.

На рівні MySQL використовуються:

- `unique` constraint для номера інвойсу;
- `decimal(15, 2)` для грошових значень;
- `enum` для статусу;
- індекси для `status`, `due_date` та `created_at`.

Frontend додатково виконує валідацію форми через `vee-validate` і `zod`, але backend залишається остаточним рівнем перевірки.

## Frontend-функціональність

Реалізовано такі сторінки:

### Список інвойсів

```text
/invoices
```

На сторінці відображаються:

- номер інвойсу;
- постачальник;
- загальна сума;
- статус;
- дата оплати;
- кількість інвойсів.

Також реалізовані loading та error states. Рядок інвойсу є клікабельним і веде на сторінку деталей.

### Деталі інвойсу

```text
/invoices/{id}
```

На сторінці відображаються:

- номер інвойсу;
- постачальник;
- податковий номер;
- сума без ПДВ;
- сума ПДВ;
- загальна сума;
- валюта;
- статус із кольоровим бейджем;
- дата виставлення;
- дата оплати;
- дата останнього оновлення.

### Редагування інвойсу

На сторінці деталей доступна форма редагування:

- `net_amount`;
- `vat_amount`;
- `due_date`.

`gross_amount` автоматично перераховується на frontend.

Якщо статус інвойсу не `pending`, форма блокується. Це обмеження дублюється на backend, тому обійти його лише через API неможливо.

## Команди

```bash
make up
```

Зібрати та запустити всі Docker-контейнери.

```bash
make down
```

Зупинити контейнери.

```bash
make build
```

Перебудувати Docker-образи.

```bash
make logs
```

Переглядати логи контейнерів.

```bash
make migrate
```

Запустити Laravel migrations вручну.

```bash
make seed
```

Запустити Laravel seeders вручну.

```bash
make test
```

Запустити Laravel feature tests.

```bash
make backend-shell
```

Відкрити shell у backend-контейнері.

```bash
make frontend-shell
```

Відкрити shell у frontend-контейнері.

## Тести

У проєкті є feature tests для API, які перевіряють:

- отримання списку інвойсів;
- створення інвойсу з коректними сумами;
- помилку при неправильному `gross_amount`;
- неможливість редагування інвойсу зі статусом `approved`.

Запуск тестів:

```bash
make test
```

## Відповіді для тестового завдання

### 1. Як структуровані frontend і backend?

Проєкт має monorepo-структуру.

`backend/` — Laravel API. Контролер відповідає за HTTP-рівень, Form Request — за валідацію даних, `InvoiceService` — за бізнес-операції та транзакції, а `InvoiceResource` — за контракт JSON-відповідей.

`frontend/` — Nuxt application. Pages відповідають за маршрути, components — за UI, composables — за API-інтеграцію, types — за TypeScript-контракти, utils — за форматування сум і дат.

`infra/nginx/` містить конфігурацію Nginx для передачі PHP-запитів у Laravel PHP-FPM контейнер.

### 2. Які компроміси зроблено і чому?

Для тестового не реалізовано:

- authentication та authorization;
- ролі користувачів;
- audit log;
- soft delete;
- пошук, фільтрацію та розширене сортування;
- окрему UI-сторінку створення інвойсу.

Створення інвойсу реалізоване на рівні API через `POST /api/invoices`, оскільки це є обов’язковою backend-вимогою. Frontend сфокусований на сторінках, прямо вказаних у завданні: список, деталі та редагування.

Для грошей використано `decimal(15,2)` у MySQL. Для складнішої production-системи можна було б перейти на зберігання сум у мінорних одиницях або використати окремий Money value object.

### 3. Що було б покращено у production-версії?

У production-версії варто додати:

- authentication та role-based authorization;
- audit trail: хто, коли та які поля змінив;
- optimistic locking через поле `version` або перевірку `updated_at`;
- пошук, фільтрацію, сортування та більшу pagination-стратегію;
- OpenAPI / Swagger документацію;
- rate limiting;
- централізований error tracking;
- CI/CD pipeline;
- linter, formatter та pre-commit hooks;
- E2E-тести для frontend;
- staging та production Docker-конфігурації;
- queue для асинхронних інтеграцій;
- окрему UI-сторінку створення інвойсу.

### 4. Які UX edge cases враховано?

- loading state у списку та на сторінці деталей;
- error state під час завантаження API-даних;
- читабельне форматування дат і сум;
- кольорові статусні бейджі;
- автоматичний перерахунок `gross_amount`;
- frontend-валідація через Zod;
- server-side validation errors показуються біля конкретних полів;
- `due_date` не може бути раніше за `issue_date`;
- форма редагування недоступна для `approved` та `rejected` інвойсів;
- backend також блокує редагування не-pending інвойсів;
- seed використовує фіксовані номери інвойсів, тому повторний запуск не створює дублікати.
