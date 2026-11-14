<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../authy.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Получаем текущие данные
$stmt = mysqli_prepare($connect, "SELECT login, surname, name, email, phone, passport, birth_date FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_info') {
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $passport = trim($_POST['passport']);
        // Добавляем получение даты рождения
        $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null; 

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Некорректный E-mail';
        } elseif (!preg_match('/^8\d{10}$/', $phone)) {
            $error = 'Телефон должен быть в формате 8XXXXXXXXXX';
        } else {
            // Добавляем birth_date в запрос UPDATE
            $stmt_upd = mysqli_prepare($connect, "UPDATE users SET email = ?, phone = ?, passport = ?, birth_date = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt_upd, 'ssssi', $email, $phone, $passport, $birth_date, $user_id);
            if (mysqli_stmt_execute($stmt_upd)) {
                $success = 'Данные успешно обновлены';
                $user['email'] = $email;
                $user['phone'] = $phone;
                $user['passport'] = $passport;
                $user['birth_date'] = $birth_date;
            } else {
                $error = 'Ошибка обновления. Возможно, email уже занят.';
            }
            mysqli_stmt_close($stmt_upd);
        }
    } elseif ($action === 'change_password') {
        $old_pass = $_POST['old_password'];
        $new_pass = $_POST['new_password'];

        $stmt_p = mysqli_prepare($connect, "SELECT password FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt_p, 'i', $user_id);
        mysqli_stmt_execute($stmt_p);
        $row_p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_p));
        mysqli_stmt_close($stmt_p);

        if (password_verify($old_pass, $row_p['password'])) {
            if (mb_strlen($new_pass) < 8) {
                $error = 'Новый пароль должен быть минимум 8 символов';
            } else {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt_upd = mysqli_prepare($connect, "UPDATE users SET password = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt_upd, 'si', $hash, $user_id);
                mysqli_stmt_execute($stmt_upd);
                mysqli_stmt_close($stmt_upd);
                $success = 'Пароль успешно изменен';
            }
        } else {
            $error = 'Неверный текущий пароль';
        }
    }
}
?>