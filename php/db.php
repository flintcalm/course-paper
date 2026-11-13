<?php
    $connect = mysqli_connect('localhost', 'root', '', 'airclick');

    if (!$connect) {
        die("Ошибка подключения: " . mysqli_connect_error());
    }
    mysqli_set_charset($connect, "utf8mb4");
?>