## Secunda API — Тестовое задание (Laravel 12 + Sail)

REST API для справочника Организаций, Зданий и Видов деятельности.

### Запуск
- Выполнить:
  - `docker compose up -d`

- Что произойдёт автоматически:
  - Установятся PHP-зависимости (`composer install`) внутри контейнера
  - Создастся `.env` (если отсутствует) и сгенерируется `APP_KEY`
  - Поднимется MySQL и создастся БД `secunda`
  - Применятся миграции и сиды
  - Сгенерируется Swagger (`/api/documentation`)

- Приложение: `http://localhost`

### Аутентификация
У всех запросов должен быть заголовок:
- `X-API-Key: dev-secret-key`

### Swagger
- UI: `http://localhost/api/documentation`
- JSON: `http://localhost/api/docs?api-docs.json`

### API (префикс `/api/v1`)
- GET `/activities` — дерево видов деятельности (до 3 уровней)
- GET `/buildings` — список зданий
- GET `/organizations` — список организаций, фильтр: `?name=...`
- GET `/organizations/{id}` — карточка организации
- GET `/buildings/{building}/organizations` — организации в здании
- GET `/activities/{activity}/organizations` — организации по виду деятельности (включая вложенные)
- GET `/organizations/near?lat=..&lng=..&radius_km=..` — в радиусе от точки (bbox)
- GET `/organizations/in-rect?lat_min=..&lat_max=..&lng_min=..&lng_max=..` — в прямоугольнике
- GET `/organizations/search?name=..&activity=..` — поиск по названию и/или деятельности

### Тесты
- Через Docker Compose:
  - `docker compose exec laravel php artisan test`
  - или: `docker compose exec laravel ./vendor/bin/phpunit`

Если контейнеры ещё не запущены — сначала `docker compose up -d`.
