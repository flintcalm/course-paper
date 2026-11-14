<?php
session_start();
require_once 'db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: ../authy.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$booking_id = (int)($_POST['booking_id'] ?? 0);

if ($booking_id > 0) {
    // Проверяем, что эта бронь принадлежит текущему пользователю, и берем данные рейса
    $sql = "SELECT b.id, b.status, b.flight_id, b.class, b.departure_date, f.departure_time 
            FROM bookings b
            JOIN flights f ON b.flight_id = f.id
            WHERE b.id = ? AND b.user_id = ?";
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $booking_id, $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if ($booking) {
        $new_status = '';
        
        // 1. ОПЛАТА (подтверждение)
        if ($action === 'confirm' && $booking['status'] === 'active') {
            $new_status = 'confirmed';
        }
        // 2. ОТМЕНА
        elseif ($action === 'cancel' && in_array($booking['status'], ['active', 'confirmed'])) {
            $new_status = 'cancelled';
        }
        // 3. ВОССТАНОВЛЕНИЕ
        elseif ($action === 'restore' && $booking['status'] === 'cancelled') {
            // Проверяем, что до вылета осталось больше 1 часа
            $dep_datetime = strtotime($booking['departure_date'] . ' ' . $booking['departure_time']);
            if ($dep_datetime <= (time() + 3600)) {
                $_SESSION['action_error'] = 'Нельзя восстановить билет: до вылета осталось менее 1 часа.';
                header('Location: ../my-bookings.php');
                exit;
            }

            // Проверяем наличие свободных мест
            $limits = ['economy' => 50, 'business' => 20, 'first' => 5];
            $max_seats = $limits[$booking['class']] ?? 50;

            $stmt_check = mysqli_prepare($connect, "
                SELECT COUNT(*) as booked
                FROM passengers p
                JOIN bookings b ON p.booking_id = b.id
                WHERE b.flight_id = ? AND b.class = ? AND b.departure_date = ? AND b.status IN ('active', 'confirmed')
            ");
            mysqli_stmt_bind_param($stmt_check, 'iss', $booking['flight_id'], $booking['class'], $booking['departure_date']);
            mysqli_stmt_execute($stmt_check);
            $res_check = mysqli_stmt_get_result($stmt_check);
            $row_check = mysqli_fetch_assoc($res_check);
            mysqli_stmt_close($stmt_check);

            if ($row_check['booked'] >= $max_seats) {
                $_SESSION['action_error'] = 'Нельзя восстановить билет: на этот рейс в данном классе уже нет свободных мест.';
                header('Location: ../my-bookings.php');
                exit;
            }

            $new_status = 'active'; // Восстанавливаем как активную (ожидает оплаты)
        }

        // Если статус валиден - обновляем БД
        if ($new_status) {
            $stmt_upd = mysqli_prepare($connect, "UPDATE bookings SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt_upd, 'si', $new_status, $booking_id);
            mysqli_stmt_execute($stmt_upd);
            mysqli_stmt_close($stmt_upd);
        }
    }
}

// Возвращаем обратно в личный кабинет
header('Location: ../my-bookings.php');
exit;