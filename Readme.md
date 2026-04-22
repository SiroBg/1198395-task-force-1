# Личный проект «TaskForce»

* Студент: [Борис Глинский](https://up.htmlacademy.ru/yii-individual/1/user/1198395).
* Наставник: [Сергей Парфенов](https://htmlacademy.ru/profile/id926645).

---

# Установка приложения

- клонируйте репозиторий `git clone https://github.com/SiroBg/1198395-task-force-1.git`
- установите необходимые библиотеки через composer `composer install`
- создайте файл окружения `.env` на основе файла `.env.example`
- выполните команду в консоли `php init-db-cli.php`, чтобы создать базу данных
- создайте sql файлы с начальными городами и категориями, если они отсутствуют, командой `php sql-fill-cli.php`
- выполните миграции `./yii migrate --interactive=0`

# Проект поддерживает юнит-тесты

В директории проекта запустите тесты командой `./vendor/bin/codecept run`

# Создание фикстур

`./yii fixture/generate-all --count=20`
`./yii fixture/load '*'`