<?php
session_start();
require_once 'db.php'; // Так как файл лежит в папке php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../reg.php');
    exit;
}

$login      = trim($_POST['login']      ?? '');
$password   = $_POST['password']        ?? '';
$surname    = trim($_POST['surname']    ?? '');
$name       = trim($_POST['name']       ?? '');
$patronymic = trim($_POST['patronymic'] ?? '');
$gender     = $_POST['gender']          ?? '';
$phone      = trim($_POST['phone']      ?? '');
$email      = trim($_POST['email']      ?? '');

$errors = [];

if (!preg_match('/^[a-zA-Z0-9]{6,}$/', $login)) {
    $errors[] = 'Логин: минимум 6 символов, только латиница и цифры';
}
if (mb_strlen($password) < 8) {
    $errors[] = 'Пароль: минимум 8 символов';
}
if ($surname === '')    $errors[] = 'Укажите фамилию';
if ($name === '')       $errors[] = 'Укажите имя';
if ($gender !== 'male' && $gender !== 'female') {
    $errors[] = 'Выберите пол';
}
if (!preg_match('/^8\d{10}$/', $phone)) {
    $errors[] = 'Телефон должен быть в формате 8XXXXXXXXXX';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Некорректный E-mail';
}

// Проверка уникальности
if (!$errors) {
    $stmt = mysqli_prepare($connect, 'SELECT login, email FROM users WHERE login = ? OR email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ss', $login, $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($res)) {
        if ($row['login'] === $login) $errors[] = 'Такой логин уже занят';
        if ($row['email'] === $email) $errors[] = 'Такой E-mail уже зарегистрирован';
    }
    mysqli_stmt_close($stmt);
}

// Если есть ошибки — сохраняем их и старые данные в сессию и возвращаем назад
if ($errors) {
    $_SESSION['reg_errors'] = $errors;
    $_SESSION['reg_old'] = $_POST;
    header('Location: ../reg.php');
    exit;
}

// Хэшируем и записываем в БД
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
    $connect,
    'INSERT INTO users (login, password, surname, name, patronymic, gender, phone, email)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
mysqli_stmt_bind_param(
    $stmt, 'ssssssss',
    $login, $passwordHash, $surname, $name, $patronymic, $gender, $phone, $email
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['user_id']  = mysqli_insert_id($connect);
    $_SESSION['user_name'] = $name;
    header('Location: ../index.html');
    exit;
} else {
    echo 'Ошибка записи: ' . mysqli_error($connect);
}
mysqli_stmt_close($stmt);