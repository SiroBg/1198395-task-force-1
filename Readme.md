# Личный проект «TaskForce»

* Студент: [Борис Глинский](https://up.htmlacademy.ru/yii-individual/1/user/1198395).
* Наставник: [Сергей Парфенов](https://htmlacademy.ru/profile/id926645).

---

# Установка приложения

- клонируйте репозиторий `git clone https://github.com/SiroBg/1198395-task-force-1.git`
- установите необходимые библиотеки через composer `composer install`
- создайте файл конфигурации `config\config.php` на основе файла `config\config.sample.php`
- накатите схему базы данных из файла `db\01_schema.sql`
- запустите команду `php sql-fill-cli.php` чтобы создать sql файлы с начальными категориями и городами.

# Проект поддерживает юнит-тесты

В директории проекта запустите тесты командой `./vendor/bin/codecept run`

# Запуск проекта через docker

`docker compose up -d`