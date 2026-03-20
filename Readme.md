# Личный проект «TaskForce»

* Студент: [Борис Глинский](https://up.htmlacademy.ru/yii-individual/1/user/1198395).
* Наставник: [Сергей Парфенов](https://htmlacademy.ru/profile/id926645).

---

# Установка приложения

- клонируйте репозиторий `git clone https://github.com/SiroBg/1198395-task-force-1.git`
- установите необходимые библиотеки через composer `composer install`
- создайте файл окружения `.env` на основе файла `.env.example`
- накатите схему базы данных из файла `db\01_schema.sql` и выполните запросы в файлах `db\categories.sql` и `db\cities`

# Проект поддерживает юнит-тесты

В директории проекта запустите тесты командой `./vendor/bin/codecept run`

# Запуск проекта через docker

`docker compose up -d`