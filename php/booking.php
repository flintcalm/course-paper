<?php
// Проверяет данные бронирования и выполняет операции с билетами.
session_start();
require_once __DIR__ . '/db.php';
$current_page = 'booking.php';

// 1. Проверка авторизации (если гость — отправляем на вход)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../authy.php');
    exit;
}

// Обрабатываем изменение статуса уже созданного бронирования.
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

// Проверяем данные пассажира и создаём новое бронирование.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flight_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $flight_id = filter_input(INPUT_POST, 'flight_id', FILTER_VALIDATE_INT);
    $class = $_POST['class'] ?? '';
    $departure_date = $_POST['departure_date'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $patronymic = trim($_POST['patronymic'] ?? '');
    $birth_date = $_POST['birth_date'] ?? '';
    $ticket_type = $_POST['ticket_type'] ?? 'adult';
    $gender = $_POST['gender'] ?? '';
    $document = trim($_POST['document'] ?? '');

    $allowed_classes = ['economy' => 50, 'business' => 20, 'first' => 5];
    $date_obj = DateTime::createFromFormat('!Y-m-d', $departure_date);
    $valid_date = $date_obj && $date_obj->format('Y-m-d') === $departure_date;

    if (!$flight_id || !isset($allowed_classes[$class]) ||
        !$valid_date || $departure_date < date('Y-m-d') ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $surname === '' || $name === '' || $document === '' ||
        !in_array($ticket_type, ['adult', 'child'], true) ||
        !in_array($gender, ['male', 'female'], true)) {
        $_SESSION['booking_error'] = 'Проверьте данные пассажира, рейса и дату вылета.';
        header('Location: ../booking.php?flight_id=' . (int)$flight_id . '&class=' . urlencode($class) . '&departure=' . urlencode($departure_date));
        exit;
    }

    $birth_obj = DateTime::createFromFormat('!Y-m-d', $birth_date);
    if (!$birth_obj || $birth_obj->format('Y-m-d') !== $birth_date || $birth_date > date('Y-m-d')) {
        $_SESSION['booking_error'] = 'Укажите корректную дату рождения пассажира.';
        header('Location: ../booking.php?flight_id=' . (int)$flight_id . '&class=' . urlencode($class) . '&departure=' . urlencode($departure_date));
        exit;
    }

    // Возраст проверяется на сервере, чтобы нельзя было обойти ограничения браузера.
    $passenger_age = $birth_obj->diff(new DateTime('today'))->y;
    if (($ticket_type === 'adult' && $passenger_age < 18) ||
        ($ticket_type === 'child' && $passenger_age >= 18)) {
        $_SESSION['booking_error'] = $ticket_type === 'adult'
            ? 'Для взрослого билета пассажиру должно быть 18 лет или больше.'
            : 'Для детского билета пассажир должен быть младше 18 лет.';
        header('Location: ../booking.php?flight_id=' . (int)$flight_id . '&class=' . urlencode($class) . '&departure=' . urlencode($departure_date));
        exit;
    }

    // Lock the flight row so concurrent booking requests cannot both take the last seat.
    mysqli_begin_transaction($connect);
    try {
        $stmt_flight_lock = mysqli_prepare($connect, 'SELECT id FROM flights WHERE id = ? FOR UPDATE');
        mysqli_stmt_bind_param($stmt_flight_lock, 'i', $flight_id);
        mysqli_stmt_execute($stmt_flight_lock);
        $flight_exists = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_flight_lock));
        mysqli_stmt_close($stmt_flight_lock);

        if (!$flight_exists) {
            throw new RuntimeException('Рейс не найден.');
        }

        $stmt_check = mysqli_prepare($connect, "
            SELECT COUNT(*) AS booked
            FROM passengers p
            JOIN bookings b ON p.booking_id = b.id
            WHERE b.flight_id = ? AND b.class = ? AND b.departure_date = ?
            AND b.status IN ('active', 'confirmed')
        ");
        mysqli_stmt_bind_param($stmt_check, 'iss', $flight_id, $class, $departure_date);
        mysqli_stmt_execute($stmt_check);
        $row_check = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_check));
        mysqli_stmt_close($stmt_check);

        if ((int)$row_check['booked'] >= $allowed_classes[$class]) {
            throw new RuntimeException('На этот рейс закончились места в выбранном классе.');
        }

        $code = 'AC-' . strtoupper(bin2hex(random_bytes(4)));
        $stmt = mysqli_prepare($connect, 'INSERT INTO bookings (code, user_id, flight_id, class, departure_date, email, phone) VALUES (?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'siissss', $code, $user_id, $flight_id, $class, $departure_date, $email, $phone);
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Не удалось создать бронирование.');
        }
        $booking_id = mysqli_insert_id($connect);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($connect, 'INSERT INTO passengers (booking_id, surname, name, patronymic, birth_date, ticket_type, gender, document) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'isssssss', $booking_id, $surname, $name, $patronymic, $birth_date, $ticket_type, $gender, $document);
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Не удалось сохранить данные пассажира.');
        }
        mysqli_stmt_close($stmt);

        mysqli_commit($connect);
        header('Location: ../my-bookings.php');
        exit;
    } catch (Throwable $e) {
        mysqli_rollback($connect);
        error_log('AirClick booking error: ' . $e->getMessage());
        $_SESSION['booking_error'] = $e instanceof RuntimeException ? $e->getMessage() : 'Не удалось оформить бронирование. Попробуйте позже.';
        header('Location: ../booking.php?flight_id=' . (int)$flight_id . '&class=' . urlencode($class) . '&departure=' . urlencode($departure_date));
        exit;
    }
}

// Подготавливаем данные для отображения выбранного рейса.
// 2. Получаем параметры из URL (ID рейса, класс, дата)
$flight_id = (int)($_GET['flight_id'] ?? 0);
$class = $_GET['class'] ?? 'economy';
$departure_date = $_GET['departure'] ?? date('Y-m-d');

// Заполняем контактные поля данными текущего пользователя.
// 3. Получаем данные пользователя из БД
$stmt = mysqli_prepare($connect, "SELECT surname, name, patronymic, phone, email, gender, birth_date, passport FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$user) {
    die('Ошибка: пользователь не найден. <a href="../authy.php">Выйти</a>');
}

// Загружаем рейс и рассчитываем стоимость билета.
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