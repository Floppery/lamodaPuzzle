#Lamora quest

## Установка
#### Установка зависимостей
``` shell
php composer.phar install
```
#### Создание базы данных
``` shell
php bin/console d:d:c
```
#### Загрузка миграции
``` shell
php bin/console d:m:m -n
```
#### Загрузка фикстур (наполнение базы данных)
``` shell
php bin/console d:f:l -n
```
#### Запуск на локальном сервере
Для запуска локального сервера нужно установить [symfony cli](https://symfony.com/download)
``` shell
symfony server:start
```
Подробнее: https://symfony.com/doc/current/setup/symfony_server.html


## Api doc
### Описание api
Где {{host}} имя хоста
``` shell
{{host}}/api/doc.json
```
По умолчанию для локального сервера
``` shell
http://127.0.0.1:8000/api/doc.json
```

### Запросы

#### Создание контейнера
POST {{host}}/api/cargo
#### Получение списка контейнеров
GET {{host}}/api/cargo
#### Получение контейнера и его содержимого
GET {{host}}/api/cargo/{{id}}
#### Поиск контейнеров, содержащих товары без фото
GET {{host}}/api/cargo/findUnique 

Пример использования можно посмотреть в файле [Cargo.http](tests/Cargo.http)