<?php
// Проверяет и сохраняет сообщение из формы обратной связи.
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../contacts.php');
    exit;
}

// Получаем контактные данные и текст обращения из формы.
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $subject === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = 'Проверьте заполнение обязательных полей и адрес электронной почты.';
    header('Location: ../contacts.php?error=1');
    exit;
}

$stmt = mysqli_prepare($connect, 'INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
if (!$stmt) {
    error_log('AirClick contact prepare error: ' . mysqli_error($connect));
    $_SESSION['contact_error'] = 'Не удалось отправить сообщение. Попробуйте позже.';
    header('Location: ../contacts.php?error=1');
    exit;
}

mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $phone, $subject, $message);
$ok = mysqli_stmt_execute($stmt);
if (!$ok) {
    error_log('AirClick contact insert error: ' . mysqli_stmt_error($stmt));
}
mysqli_stmt_close($stmt);

if ($ok) {
    header('Location: ../contacts.php?success=1');
} else {
    $_SESSION['contact_error'] = 'Не удалось отправить сообщение. Попробуйте позже.';
    header('Location: ../contacts.php?error=1');
}
exit;
