<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// Защита: только для админа
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$current_page = 'admin.php';

// 1. Получаем всех пользователей для выпадающего списка и таблицы
$users_result = mysqli_query($connect, "SELECT id, login, surname, name, email, phone, role, created_at FROM users ORDER BY id DESC");
$users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);

// 2. Обрабатываем выбор пользователя
$selected_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$active_bookings = [];
$past_bookings = [];

if ($selected_user_id > 0) {
    $sql = "SELECT b.*, u.surname AS user_surname, u.name AS user_name, u.login AS user_login,
                f.from_city, f.to_city, f.departure_time, f.arrival_time, f.duration, f.airline, f.flight_number,
                fc.name AS class_name
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN flights f ON b.flight_id = f.id
            JOIN fare_classes fc ON b.class = fc.code
            WHERE b.user_id = ?
            ORDER BY b.departure_date DESC, b.created_at DESC";
            
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $selected_user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $all_bookings = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    // Разделяем на активные и историю
    foreach ($all_bookings as $b) {
        if ($b['status'] === 'active' || $b['status'] === 'confirmed') {
            $active_bookings[] = $b;
        } else {
            $past_bookings[] = $b;
        }
    }
}

// 3. Получаем сообщения
$messages_result = mysqli_query($connect, "SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = mysqli_fetch_all($messages_result, MYSQLI_ASSOC);

// 4. Получаем рейсы (для CRUD)
$flights_result = mysqli_query($connect, "SELECT * FROM flights ORDER BY id DESC LIMIT 50");
$flights = mysqli_fetch_all($flights_result, MYSQLI_ASSOC);

// Редактирование рейса
$edit_flight = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $stmt = mysqli_prepare($connect, "SELECT * FROM flights WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $edit_id);
    mysqli_stmt_execute($stmt);
    $edit_flight = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}
?>