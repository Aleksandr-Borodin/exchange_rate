# exchange_rate

## Запуск проекта

Перейти в папку проекта:

```bash
cd ~/exchange_rate
```

Собрать Docker-контейнеры:

```bash
docker compose build --no-cache
```

Запустить контейнеры:

```bash
docker compose up -d
```

## Сбор курсов

Запустить постановку очереди для будущего сбора данных за указанное количество дней:

```bash
docker compose exec php php bin/console rate:collect n
```

Например, за 10 дней:

```bash
docker compose exec php php bin/console rate:collect 10
```

## Получение курса

Получить курс валюты за указанную дату:

```bash
docker compose exec php php bin/console rate:get YYYY-MM-DD CURRENCY
```

Например:

```bash
docker compose exec php php bin/console rate:get 2026-09-29 USD
```

По умолчанию используется:

* базовая валюта — `RUB`;
* источник — `cbr`.

Можно указать базовую валюту:

```bash
docker compose exec php php bin/console rate:get 2026-09-29 USD EUR
```

Можно указать источник:

```bash
docker compose exec php php bin/console rate:get 2026-09-29 USD RUB --source=cbr
```

Команда возвращает курс за указанную дату, разницу с предыдущим торговым днём и статус получения данных.


## Запуск worker-а (вручную; worker запускается автоматически с контейнером)

```bash
docker compose exec php php bin/console rate:worker
```
