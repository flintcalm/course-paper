<?php
// Создаёт подключение к базе данных и настраивает кодировку соединения.
mysqli_report(MYSQLI_REPORT_OFF);

$connect = mysqli_connect('localhost', 'root', '', 'airclick');
if (!$connect) {
    error_log('AirClick database connection failed: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Временно не удалось подключиться к базе данных. Попробуйте позже.');
}
mysqli_set_charset($connect, 'utf8mb4');
