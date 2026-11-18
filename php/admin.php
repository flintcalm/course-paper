<?php
// Проверяет права администратора и обрабатывает действия панели управления.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

// Защита: только для админа
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Обрабатываем действия администратора, отправленные из форм.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'change_status') {
        $booking_id = (int)$_POST['booking_id'];
        $status = $_POST['status'];
        
        if (in_array($status, ['active', 'confirmed', 'cancelled', 'completed'])) {
            $stmt = mysqli_prepare($connect, "UPDATE bookings SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'si', $status, $booking_id);
            mysqli_stmt_execute($stmt);
        }
    }
    elseif ($action === 'delete_booking') {
        $booking_id = (int)$_POST['booking_id'];
        
        $stmt = mysqli_prepare($connect, "DELETE FROM passengers WHERE booking_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $booking_id);
        mysqli_stmt_execute($stmt);
        
        $stmt = mysqli_prepare($connect, "DELETE FROM bookings WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $booking_id);
        mysqli_stmt_execute($stmt);
    }
    elseif ($action === 'delete_user') {
        $user_id = (int)$_POST['user_id'];
        
        if ($user_id !== $_SESSION['user_id']) {
            $stmt = mysqli_prepare($connect, "SELECT id FROM bookings WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $bookings = mysqli_fetch_all($res, MYSQLI_ASSOC);
            
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
            $stmt = mysqli_prepare($connect, "UPDATE flights SET from_city=?, to_city=?, departure_time=?, arrival_time=?, duration=?, duration_min=?, airline=?, flight_number=?, aircraft=?, price=?, stops=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'sssssisssdsi', $from_city, $to_city, $departure_time, $arrival_time, $duration, $duration_min, $airline, $flight_number, $aircraft, $price, $stops, $id);
        } else {
            $stmt = mysqli_prepare($connect, "INSERT INTO flights (from_city, to_city, departure_time, arrival_time, duration, duration_min, airline, flight_number, aircraft, price, stops) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssssisssds', $from_city, $to_city, $departure_time, $arrival_time, $duration, $duration_min, $airline, $flight_number, $aircraft, $price, $stops);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    elseif ($action === 'delete_flight') {
        $flight_id = (int)$_POST['flight_id'];
        $stmt = mysqli_prepare($connect, "DELETE FROM flights WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $flight_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $redirect_url = '../admin.php';
    if (isset($_POST['current_user_id']) && (int)$_POST['current_user_id'] > 0) {
        $redirect_url .= '?user_id=' . (int)$_POST['current_user_id'];
    }
    header('Location: ' . $redirect_url);
    exit;
}

$current_page = 'admin.php';

// Подготавливаем данные, которые используются в фильтрах панели.
// Список городов для выпадающих списков
$cities = ['Москва', 'Санкт-Петербург', 'Сочи', 'Париж', 'Нью-Йорк', 'Токио', 'Дубай', 'Лондон', 'Рим', 'Пекин', 'Стамбул', 'Бангкок', 'Анталья', 'Казань', 'Екатеринбург'];

// Загружаем пользователей для выбора записи в панели.
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

// Загружаем обращения, отправленные через форму контактов.
// 3. Получаем сообщения
$messages_result = mysqli_query($connect, "SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = mysqli_fetch_all($messages_result, MYSQLI_ASSOC);

// Формируем список рейсов с учётом выбранных фильтров.
// 4. Получаем рейсы с учетом фильтров и сортировки
$f_from = $_GET['f_from'] ?? '';
$f_to = $_GET['f_to'] ?? '';
$f_airline = $_GET['f_airline'] ?? 'all';
$f_price = $_GET['f_price'] ?? 'all';
$f_limit = $_GET['f_limit'] ?? '100';

$sort = 'id_desc';
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