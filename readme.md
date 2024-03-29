#Lamora quest (PHP-головоломка)

## Задача

В Lamoda есть собственная фотостудия, на которой мы делаем фотосъемку всех новых
товаров. Товары приходят к нам на наш [склад](https://habr.com/ru/company/lamoda/blog/432394/), после чего отправляются в контейнерах на
фотостудию. Мы не фотографируем несколько раз один и тот же товар. Помоги Lamoda
оптимизировать логистику между фотостудией и складом.

Игроку необходимо написать систему (веб-сервис), который будет иметь REST JSON API
для:

1. Создания контейнеров (id контейнера, название, состав - массив товаров (id товара,
название);
2. Получения списка контейнеров и отдельного контейнера по ID.

C помощью этого API решить следующую головоломку: есть 1000 контейнеров, в каждом
из них по 10 товаров, среди всех этих товаров только 100 уникальных, все остальные
повторяются. Нужно вернуть список контейнеров, которые содержат все 100 уникальных
товаров хотя бы по одному. Товары в контейнерах распределены случайным, но
известным образом.

**Эта ситуация основана на вымышленном условии, что нам нельзя вскрывать контейнеры
и что все товары перемешаны.*

**Даст вам преимущество перед другими игроками:**

1. Простота разворачивания и запуска;
2. Формирование OpenAPI v3 спецификации на созданное API;
3. Применение виртуализации (Docker etc.);
4. Если ваше решение будет параметризованно количеством контейнеров и
количеством уникальных товаров и емкостью контейнера;
5. Если список возвращаемых контейнеров будет минимальным из всех возможных;
6. Если вы дополните решение аргументацией, почему ваш алгоритм оптимален по
сложности;
7. Если ваше решение будет дополнено генератором контейнеров и товаров;
8. Наличие тестов в достаточном объеме.

Фреймворк, библиотеки - на выбор игрока.

У этой головоломки может быть множество решений, и мы примем от вас любое.

------

## Установка

Запустить up.cmd или выполнить команды

#### Компилируем образы Docker
``` shell
docker compose build
```

#### Запускаем контейнеры
``` shell
docker compose up -d
```

------

Запустить install.cmd или выполнить команды

#### Установка зависимостей
``` shell
docker exec LamodaPuzzle_php composer install
```
#### Создание базы данных
``` shell
docker exec LamodaPuzzle_php symfony console d:d:c --if-not-exists
```
#### Загрузка миграции
``` shell
docker exec LamodaPuzzle_php symfony console d:m:m -n
```
#### Загрузка фикстур (наполнение базы данных)
``` shell
docker exec LamodaPuzzle_php symfony console d:f:l -n
```

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
```http request
POST {{host}}/api/cargo
```
#### Получение списка контейнеров
```http request
GET {{host}}/api/cargo
```
#### Получение контейнера и его содержимого
```http request
GET {{host}}/api/cargo/{{id}}
```
#### Поиск контейнеров, содержащих товары без фото
```http request
GET {{host}}/api/cargo/findUnique
``` 

Пример использования можно посмотреть в файле [Cargo.http](tests/Cargo.http)