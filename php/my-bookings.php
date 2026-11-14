<?php
session_start();
require_once 'db.php';
require_once 'cron_status.php';
$current_page = 'my-bookings.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../authy.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Получаем бронирования
$sql = "SELECT b.*, f.from_city, f.to_city, f.departure_time, f.arrival_time,
                f.duration, f.airline, f.flight_number, f.stops,
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
$bookings = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// 2. Получаем пассажиров для всех этих броней
$booking_ids = array_column($bookings, 'id');
$passengers_by_booking = [];

if (!empty($booking_ids)) {
    // Создаем строку вида ?, ?, ? для SQL запроса
    $in_query = implode(',', array_fill(0, count($booking_ids), '?'));
    $sql_p = "SELECT * FROM passengers WHERE booking_id IN ($in_query)";
    
    $stmt_p = mysqli_prepare($connect, $sql_p);
    $types = str_repeat('i', count($booking_ids));
    mysqli_stmt_bind_param($stmt_p, $types, ...$booking_ids);
    mysqli_stmt_execute($stmt_p);
    $res_p = mysqli_stmt_get_result($stmt_p);
    
    while ($p = mysqli_fetch_assoc($res_p)) {
        $passengers_by_booking[$p['booking_id']][] = $p;
    }
    mysqli_stmt_close($stmt_p);
}

// 3. Распределяем по массивам и добавляем пассажиров
$active_bookings = [];
$past_bookings = [];

foreach ($bookings as $b) {
    $b['passengers'] = $passengers_by_booking[$b['id']] ?? []; // Прикрепляем пассажиров
    
    if ($b['status'] === 'active' || $b['status'] === 'confirmed') {
        $active_bookings[] = $b;
    } else {
        $past_bookings[] = $b;
    }
}
?>