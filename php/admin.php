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

// Список городов для выпадающих списков
$cities = ['Москва', 'Санкт-Петербург', 'Сочи', 'Париж', 'Нью-Йорк', 'Токио', 'Дубай', 'Лондон', 'Рим', 'Пекин', 'Стамбул', 'Бангкок', 'Анталья', 'Казань', 'Екатеринбург'];

// 1. Получаем всех пользователей
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
    $all_bookings = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

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

// 4. Получаем рейсы с учетом фильтров и сортировки
$f_from = $_GET['f_from'] ?? '';
$f_to = $_GET['f_to'] ?? '';
$f_airline = $_GET['f_airline'] ?? 'all';
$f_price = $_GET['f_price'] ?? 'all';
$f_limit = $_GET['f_limit'] ?? '100';

// Определяем активную сортировку (проверяем по очереди, какой из 4-х списков был выбран)
$sort = 'id_desc'; // по умолчанию
if (!empty($_GET['sort_id'])) {
    $sort = $_GET['sort_id'];
} elseif (!empty($_GET['sort_price'])) {
    $sort = $_GET['sort_price'];
} elseif (!empty($_GET['sort_dur'])) {
    $sort = $_GET['sort_dur'];
} elseif (!empty($_GET['sort_time'])) {
    $sort = $_GET['sort_time'];
}

$sql_flights = "SELECT * FROM flights WHERE 1=1";
$params_flights = [];
$types_flights = '';

if ($f_from) {
    $sql_flights .= " AND from_city LIKE ?";
    $params_flights[] = "%$f_from%";
    $types_flights .= 's';
}
if ($f_to) {
    $sql_flights .= " AND to_city LIKE ?";
    $params_flights[] = "%$f_to%";
    $types_flights .= 's';
}
if ($f_airline !== 'all') {
    $sql_flights .= " AND airline = ?";
    $params_flights[] = $f_airline;
    $types_flights .= 's';
}
if ($f_price !== 'all') {
    if ($f_price === '50000+') {
        $sql_flights .= " AND price >= 50000";
    } else {
        list($min, $max) = explode('-', $f_price);
        $sql_flights .= " AND price BETWEEN ? AND ?";
        $params_flights[] = (float)$min;
        $params_flights[] = (float)$max;
        $types_flights .= 'dd';
    }
}

// Сортировка
switch ($sort) {
    case 'id_asc': $sql_flights .= " ORDER BY id ASC"; break;
    case 'price_asc': $sql_flights .= " ORDER BY price ASC"; break;
    case 'price_desc': $sql_flights .= " ORDER BY price DESC"; break;
    case 'duration_asc': $sql_flights .= " ORDER BY duration_min ASC"; break;
    case 'duration_desc': $sql_flights .= " ORDER BY duration_min DESC"; break;
    case 'time_asc': $sql_flights .= " ORDER BY departure_time ASC"; break;
    case 'time_desc': $sql_flights .= " ORDER BY departure_time DESC"; break;
    default: $sql_flights .= " ORDER BY id DESC"; break;
}

// Лимит
if ($f_limit !== 'all') {
    $limit_val = (int)$f_limit;
    $sql_flights .= " LIMIT $limit_val";
}

$stmt_flights = mysqli_prepare($connect, $sql_flights);
if ($params_flights) {
    mysqli_stmt_bind_param($stmt_flights, $types_flights, ...$params_flights);
}
mysqli_stmt_execute($stmt_flights);
$flights = mysqli_fetch_all(mysqli_stmt_get_result($stmt_flights), MYSQLI_ASSOC);
mysqli_stmt_close($stmt_flights);

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