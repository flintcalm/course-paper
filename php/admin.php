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

// Получаем всех пользователей (кроме паролей)
$users_result = mysqli_query($connect, "SELECT id, login, surname, name, email, phone, role, created_at FROM users ORDER BY id DESC");
$users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);

// Получаем все бронирования с данными о пользователе и рейсе
$sql = "SELECT b.*, u.surname AS user_surname, u.name AS user_name, u.login AS user_login,
            f.from_city, f.to_city, f.departure_time
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN flights f ON b.flight_id = f.id
        ORDER BY b.created_at DESC";
$bookings_result = mysqli_query($connect, $sql);
$bookings = mysqli_fetch_all($bookings_result, MYSQLI_ASSOC);
?>