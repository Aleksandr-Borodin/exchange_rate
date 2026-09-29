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
docker compose exec php php bin/console cbr:collect n
```

Например, за 10 дней:

```bash
docker compose exec php php bin/console cbr:collect 10
```
