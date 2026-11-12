<?php
session_start();
require_once 'db.php';

// 1. Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: ../authy.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. Получаем бронирования пользователя, соединяя с рейсами и классами
// Также считаем количество пассажиров в каждом бронировании
$sql = "SELECT b.*, f.from_city, f.to_city, f.departure_time, f.arrival_time,
                f.duration, f.duration_min, f.airline, f.flight_number, f.stops,
                fc.name AS class_name, (f.price * fc.coefficient) AS base_price,
                (SELECT COUNT(*) FROM passengers p WHERE p.booking_id = b.id) as passenger_count
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        JOIN fare_classes fc ON b.class = fc.code
        WHERE b.user_id = ?
        ORDER BY b.departure_date DESC, b.created_at DESC";

$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// 3. Делим бронирования на активные и прошедшие
$active_bookings = [];
$past_bookings = [];

foreach ($bookings as $b) {
    if ($b['status'] === 'active' || $b['status'] === 'confirmed') {
        $active_bookings[] = $b;
    } else {
        $past_bookings[] = $b;
    }
}
?>