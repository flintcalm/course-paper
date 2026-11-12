<?php
    $connect = mysqli_connect('localhost', 'root', '', 'airclick');

    if (!$connect) {
        die("Ошибка подключения: " . mysqli_connect_error());
    }

?>