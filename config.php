<?php
function getDbConnection() {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbName = 'registration_authorization';
    $connection = @new mysqli($host, $user, $password);

    if ($connection->connect_error) {
        die('Connection failed: ' . $connection->connect_error);
    }
        // создание БД, если она не найдена
        $createDbQuery = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";//COLLATE utf8mb4_general_ci -сортировка и сравнение символов для таблиц в MySQL, игнорирует регистр символов при сравнении, utf8mb4_general_ci поддерживает полный набор символов Unicode, включая эмодзи
        if ($connection->query($createDbQuery) === TRUE) {
        //echo "<p class='info'>Database created successfully!</p><br>";!! выводить не надо,так как клиент (js) ожидает получить данные в формате JSON
            $connection->select_db($dbName);
        } else {
            die("Error creating database: " . $connection->error);
        }
    // подключаемся к БД
    $connection->select_db($dbName);

    // создаем таблицу пользователей
    $createTableQuery = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    ) DEFAULT CHARSET=utf8mb4;";

    if ($connection->query($createTableQuery) !== TRUE) {
        die("Error creating table: " . $connection->error);
    }

    return $connection;
}

?>
