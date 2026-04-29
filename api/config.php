<?php // Готовый конфиг под импорт full_db.sql (БД shop_exam, пользователь lehh)
const DB_HOST = '127.0.0.1';
const DB_NAME = 'shop_exam';
const DB_USER = 'lehh';
const DB_PASS = 'lehh123_!';
const DB_PORT = '3306';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
$db->set_charset('utf8mb4');

$_SERVER['db'] = $db;
