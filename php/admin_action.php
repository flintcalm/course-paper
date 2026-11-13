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

header('Location: ../admin.php');
exit;