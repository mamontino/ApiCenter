<?php

/**
 * Конфигурационный файл базы данных
 */

define('DB_USERNAME', 'vp');
define('DB_PASSWORD', 'idsgxt');
define('DB_HOST', '192.168.0.102');
define('DB_NAME', 'db_chat');
define('DB_PORT', '8080');

/**
 * Коды ответов и обработка ошибок
 */

define('ANSWER_OK', 0);
define('ANSWER_ERROR', 1);
define('ANSWER_INVALID', 2);

define("NO_WORK", "Не работает");

define("EMPTY_DATA", "Не найдено данных, удовлетворяющих запросу");
define("EMPTY_FIELD", "Все поля должны быть заполнены");

define("INVALID_FIELD", "Неверно указаны параметры запроса: ");
define("INVALID_TOKEN", "Неверно указан токен авторизации");
define("INVALID_FB_KEY", "Неверно указан ключ для отправки сообщений");

define("REQUEST_OK", "Запрос выполнен успешно");
define("REQUEST_ERROR", "Ошибка выполнения запроса: ");

define("DB_ERROR", "Ошибка подключения к базе данных");

/**
 * Ключи для API и отправки сообщений
 */

define('API_KEY', 'AAAA2UBtySo:APA91bGOxg0DNY9Ojz-BD0d4bUr-GukFBdvCtivWVjqZ8ppEHtl-BIwmINKD3R_');
define("FB_KEY", "AAAA2UBtySo:APA91bGOxg0DNY9Ojz-BD0d4bUr-GukFBdvCtivWVjqZ8ppEHtl-BIwmINKD3R_9AfguvNjJHac2AGHSjWhoVbpZ3JILBVm4gH2X48TwNHWv6uW-bWVkPbaIFPnhEw_ZczK1owGnhZAn");
