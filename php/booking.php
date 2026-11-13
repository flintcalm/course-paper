<?php
session_start();
// Подключаем БД (файл лежит в этой же папке)
require_once 'db.php';
$current_page = 'booking.php';

// 1. Проверка авторизации (если гость — отправляем на вход)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../authy.php');
    exit;
}

// 2. Получаем параметры из URL (ID рейса, класс, дата)
$flight_id = (int)($_GET['flight_id'] ?? 0);
$class = $_GET['class'] ?? 'economy';
$departure_date = $_GET['departure'] ?? date('Y-m-d'); // Если даты нет, ставим сегодня

// 3. Получаем данные пользователя из БД
$stmt = mysqli_prepare($connect, "SELECT surname, name, patronymic, phone, email, gender FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// Если пользователь вдруг не найден (например, удален)
if (!$user) {
    die('Ошибка: пользователь не найден. <a href="../authy.php">Выйти</a>');
}

// 4. Получаем данные рейса и считаем итоговую цену
$sql = "SELECT f.*, (f.price * fc.coefficient) AS final_price, fc.name AS class_name 
        FROM flights f
        JOIN fare_classes fc ON fc.code = ?
        WHERE f.id = ?";
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, 'si', $class, $flight_id);
mysqli_stmt_execute($stmt);
$flight = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// Если рейс не найден
if (!$flight) {
    die('Ошибка: рейс не найден. <a href="../flights.php">Вернуться к поиску</a>');
}
?>