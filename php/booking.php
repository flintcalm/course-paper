<?php
// booking.php
session_start();
require_once 'db.php';
$current_page = 'booking.php';

// 1. Проверка авторизации (если гость — отправляем на вход)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../authy.php');
    exit;
}

// ==========================================
// ЛОГИКА ИЗ user_booking_action.php
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['confirm', 'cancel', 'restore'])) {
    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';
    $booking_id = (int)($_POST['booking_id'] ?? 0);

    if ($booking_id > 0) {
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
            
            if ($action === 'confirm' && $booking['status'] === 'active') {
                $new_status = 'confirmed';
            }
            elseif ($action === 'cancel' && in_array($booking['status'], ['active', 'confirmed'])) {
                $new_status = 'cancelled';
            }
            elseif ($action === 'restore' && $booking['status'] === 'cancelled') {
                $dep_datetime = strtotime($booking['departure_date'] . ' ' . $booking['departure_time']);
                $time_left = $dep_datetime - time();

                if ($time_left <= 1200) {
                    $_SESSION['action_error'] = 'Нельзя восстановить билет: до вылета осталось менее 20 минут.';
                    header('Location: ../my-bookings.php');
                    exit;
                }

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

                if ($time_left > 3600) {
                    $new_status = 'active';
                    $_SESSION['action_success'] = 'Билет успешно восстановлен. Пожалуйста, оплатите его.';
                } else {
                    $new_status = 'confirmed';
                    $_SESSION['action_success'] = 'Билет успешно восстановлен и оплачен.';
                }
            }

            if ($new_status) {
                $stmt_upd = mysqli_prepare($connect, "UPDATE bookings SET status = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt_upd, 'si', $new_status, $booking_id);
                mysqli_stmt_execute($stmt_upd);
                mysqli_stmt_close($stmt_upd);
            }
        }
    }
    header('Location: ../my-bookings.php');
    exit;
}

// ==========================================
// ЛОГИКА ИЗ create_booking.php
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flight_id'])) {
    $user_id        = $_SESSION['user_id'];
    $flight_id      = (int)$_POST['flight_id'];
    $class          = $_POST['class'];
    $departure_date = $_POST['departure_date'];
    $email          = trim($_POST['email']);
    $phone          = trim($_POST['phone']);

    $surname    = trim($_POST['surname']);
    $name       = trim($_POST['name']);
    $patronymic = trim($_POST['patronymic'] ?? '');
    $birth_date = $_POST['birth_date'];
    $gender     = $_POST['gender'];
    $document   = trim($_POST['document']);

    $limits = ['economy' => 50, 'business' => 20, 'first' => 5];
    $max_seats = $limits[$class] ?? 50;

    $stmt_check = mysqli_prepare($connect, "
        SELECT COUNT(*) as booked
        FROM passengers p
        JOIN bookings b ON p.booking_id = b.id
        WHERE b.flight_id = ? AND b.class = ? AND b.departure_date = ? AND b.status IN ('active', 'confirmed')
    ");
    mysqli_stmt_bind_param($stmt_check, 'iss', $flight_id, $class, $departure_date);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    $row_check = mysqli_fetch_assoc($res_check);
    mysqli_stmt_close($stmt_check);

    if ($row_check['booked'] >= $max_seats) {
        $_SESSION['booking_error'] = 'К сожалению, на этот рейс только что закончились места в выбранном классе.';
        header('Location: ../booking.php?flight_id=' . $flight_id . '&class=' . $class . '&departure=' . $departure_date);
        exit;
    }

    $code = 'AC-' . strtoupper(bin2hex(random_bytes(3)));

    $stmt = mysqli_prepare(
        $connect,
        'INSERT INTO bookings (code, user_id, flight_id, class, departure_date, email, phone) 
        VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param(
        $stmt, 'siissss',
        $code, $user_id, $flight_id, $class, $departure_date, $email, $phone
    );

    if (!mysqli_stmt_execute($stmt)) {
        die('Ошибка при создании брони: ' . mysqli_error($connect));
    }

    $booking_id = mysqli_insert_id($connect);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare(
        $connect,
        'INSERT INTO passengers (booking_id, surname, name, patronymic, birth_date, gender, document)
        VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param(
        $stmt, 'issssss',
        $booking_id, $surname, $name, $patronymic, $birth_date, $gender, $document
    );

    if (!mysqli_stmt_execute($stmt)) {
        die('Ошибка при добавлении пассажира: ' . mysqli_error($connect));
    }
    mysqli_stmt_close($stmt);

    header('Location: ../my-bookings.php');
    exit;
}
// ==========================================

// 2. Получаем параметры из URL (ID рейса, класс, дата)
$flight_id = (int)($_GET['flight_id'] ?? 0);
$class = $_GET['class'] ?? 'economy';
$departure_date = $_GET['departure'] ?? date('Y-m-d');

// 3. Получаем данные пользователя из БД
$stmt = mysqli_prepare($connect, "SELECT surname, name, patronymic, phone, email, gender, birth_date, passport FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

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

if (!$flight) {
    die('Ошибка: рейс не найден. <a href="../flights.php">Вернуться к поиску</a>');
}
?>