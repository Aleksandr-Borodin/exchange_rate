# exchange_rate
Перейти в папку с проектом

Собрать контейнер
docker compose build --no-cache

Запустить контейнер
docker compose up -d

Запустить сбор данных за n дней
docker compose exec php php bin/console cbr:collect n
