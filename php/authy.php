<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../authy.php');
    exit;
}

$login    = trim($_POST['login'] ?? '');
$password = $_POST['password']   ?? '';

$stmt = mysqli_prepare($connect, 'SELECT id, name, password FROM users WHERE login = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $login);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    header('Location: ../index.php');
    exit;
}

// Если ошибка — возвращаем назад с сообщением
$_SESSION['auth_error'] = 'Неверный логин или пароль';
header('Location: ../authy.php');
exit;