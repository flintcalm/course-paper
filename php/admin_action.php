<?php
session_start();
require_once 'db.php';

// Защита
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'change_status') {
    $booking_id = (int)$_POST['booking_id'];
    $status = $_POST['status'];
    
    // Разрешаем только валидные статусы
    if (in_array($status, ['active', 'confirmed', 'cancelled', 'completed'])) {
        $stmt = mysqli_prepare($connect, "UPDATE bookings SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $status, $booking_id);
        mysqli_stmt_execute($stmt);
    }
}
elseif ($action === 'delete_booking') {
    $booking_id = (int)$_POST['booking_id'];
    
    // Сначала удаляем пассажиров (так как внешние ключи в БД мы отключали)
    $stmt = mysqli_prepare($connect, "DELETE FROM passengers WHERE booking_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $booking_id);
    mysqli_stmt_execute($stmt);
    
    // Затем удаляем само бронирование
    $stmt = mysqli_prepare($connect, "DELETE FROM bookings WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $booking_id);
    mysqli_stmt_execute($stmt);
}
elseif ($action === 'delete_user') {
    $user_id = (int)$_POST['user_id'];
    
    if ($user_id !== $_SESSION['user_id']) {
        // 1. Находим все бронирования пользователя
        $stmt = mysqli_prepare($connect, "SELECT id FROM bookings WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $bookings = mysqli_fetch_all($res, MYSQLI_ASSOC);
        
        // 2. Удаляем пассажиров и брони
        foreach ($bookings as $b) {
            $stmt_del_p = mysqli_prepare($connect, "DELETE FROM passengers WHERE booking_id = ?");
            mysqli_stmt_bind_param($stmt_del_p, 'i', $b['id']);
            mysqli_stmt_execute($stmt_del_p);
            mysqli_stmt_close($stmt_del_p);

            $stmt_del_b = mysqli_prepare($connect, "DELETE FROM bookings WHERE id = ?");
            mysqli_stmt_bind_param($stmt_del_b, 'i', $b['id']);
            mysqli_stmt_execute($stmt_del_b);
            mysqli_stmt_close($stmt_del_b);
        }
        
        // 3. Удаляем самого пользователя
        $stmt = mysqli_prepare($connect, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
    }
}
elseif ($action === 'delete_message') {
    $message_id = (int)$_POST['message_id'];
    $stmt = mysqli_prepare($connect, "DELETE FROM contact_messages WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $message_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
elseif ($action === 'save_flight') {
    $id = (int)$_POST['id'];
    $from_city = trim($_POST['from_city']);
    $to_city = trim($_POST['to_city']);
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $duration = trim($_POST['duration']);
    $duration_min = (int)$_POST['duration_min'];
    $airline = trim($_POST['airline']);
    $flight_number = trim($_POST['flight_number']);
    $aircraft = trim($_POST['aircraft']);
    $price = (float)$_POST['price'];
    $stops = trim($_POST['stops']);

    if ($id > 0) {
        // Обновление
        $stmt = mysqli_prepare($connect, "UPDATE flights SET from_city=?, to_city=?, departure_time=?, arrival_time=?, duration=?, duration_min=?, airline=?, flight_number=?, aircraft=?, price=?, stops=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'sssssisssdsi', $from_city, $to_city, $departure_time, $arrival_time, $duration, $duration_min, $airline, $flight_number, $aircraft, $price, $stops, $id);
    } else {
        // Добавление
        $stmt = mysqli_prepare($connect, "INSERT INTO flights (from_city, to_city, departure_time, arrival_time, duration, duration_min, airline, flight_number, aircraft, price, stops) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sssssisssds', $from_city, $to_city, $departure_time, $arrival_time, $duration, $duration_min, $airline, $flight_number, $aircraft, $price, $stops);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
elseif ($action === 'delete_flight') {
    $flight_id = (int)$_POST['flight_id'];
    // Удаляем рейс (ВНИМАНИЕ: если на рейс есть брони, SQL выдаст ошибку из-за FOREIGN KEY)
    $stmt = mysqli_prepare($connect, "DELETE FROM flights WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $flight_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
// Возвращаем обратно в админку с сохранением выбранного пользователя
$redirect_url = '../admin.php';
if (isset($_POST['current_user_id']) && (int)$_POST['current_user_id'] > 0) {
    $redirect_url .= '?user_id=' . (int)$_POST['current_user_id'];
}

header('Location: ' . $redirect_url);
exit;

header('Location: ../admin.php');
exit;