<?php
session_start();
require_once 'db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: ../authy.php');
    exit;
}

// Проверка, что данные пришли методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// 1. Получаем данные из формы
$user_id        = $_SESSION['user_id'];
$flight_id      = (int)$_POST['flight_id'];
$class          = $_POST['class'];
$departure_date = $_POST['departure_date'];
$email          = trim($_POST['email']);
$phone          = trim($_POST['phone']);

// Данные пассажира
$surname    = trim($_POST['surname']);
$name       = trim($_POST['name']);
$patronymic = trim($_POST['patronymic'] ?? '');
$birth_date = $_POST['birth_date'];
$gender     = $_POST['gender'];
$document   = trim($_POST['document']);

// 1.5. ПРОВЕРКА НАЛИЧИЯ МЕСТ
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

// 2. Генерируем уникальный код бронирования (например, AC-A1B2C3)
$code = 'AC-' . strtoupper(bin2hex(random_bytes(3)));

// 3. Сохраняем бронирование в таблицу bookings
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

// Получаем ID только что созданной брони
$booking_id = mysqli_insert_id($connect);
mysqli_stmt_close($stmt);

// 4. Сохраняем данные пассажира в таблицу passengers
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

// 5. Перенаправляем пользователя в его личный кабинет
header('Location: ../my-bookings.php');
exit;