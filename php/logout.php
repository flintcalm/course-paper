<?php
// Завершает пользовательскую сессию и возвращает на главную страницу.
session_start();
session_destroy();
header('Location: ../index.php');
exit;