# Установка приложения

- `git clone https://github.com/SiroBg/1198395-task-force-1.git`
- `composer install`
- создайте файл окружения `.env` на основе файла `.env.example`
- `php init-db-cli.php` создаёт базу данных.
- `php sql-fill-cli.php` создаёт sql файлы с начальными городами и категориями.
- `./yii migrate --interactive=0` выполняет миграции.

# Примечание

- Для работы автокомплита при выборе локации на странице добавления задания необходим
- дополнительный API ключ (yandex suggest api key).