<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Проверяем, пришли ли вообще данные из формы
    if (!$name || !$email || !$subject || !$message) {
        die("Ошибка: Не все обязательные поля заполнены. Полученные данные: <pre>" . print_r($_POST, true) . "</pre>");
    }

    $stmt = mysqli_prepare($connect, "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        die("Ошибка подготовки SQL-запроса: " . mysqli_error($connect));
    }

    mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $phone, $subject, $message);
    
    if (mysqli_stmt_execute($stmt)) {
        // Если успешно - перенаправляем
        mysqli_stmt_close($stmt);
        header('Location: ../contacts.php?success=1');
        exit;
    } else {
        // Если ошибка при вставке - показываем её
        die("Ошибка при сохранении в БД: " . mysqli_stmt_error($stmt));
    }
} else {
    echo "Скрипт запущен не через POST-запрос.";
}
?>