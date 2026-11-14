<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';
$current_page = basename($_SERVER['PHP_SELF']);

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$departure = $_GET['departure'] ?? date('Y-m-d'); // Если дата не выбрана, ставим сегодня
$class = $_GET['class'] ?? 'economy';
$sort = $_GET['sort'] ?? 'price_asc';
$airline = $_GET['airlines'] ?? 'all';
$price_range = $_GET['price_range'] ?? 'all';

// Формируем базовый SQL с подсчетом занятых мест и скрытием рейсов, до которых < 2 часов
$sql = "SELECT f.*, (f.price * fc.coefficient) AS final_price, fc.name AS class_name,
        (SELECT COUNT(*) FROM passengers p
        JOIN bookings b ON p.booking_id = b.id
        WHERE b.flight_id = f.id AND b.class = ? AND b.departure_date = ? AND b.status IN ('active', 'confirmed')) AS booked_seats
        FROM flights f
        JOIN fare_classes fc ON fc.code = ?
        WHERE 1=1
        AND STR_TO_DATE(CONCAT(?, ' ', f.departure_time), '%Y-%m-%d %H:%i') > DATE_ADD(NOW(), INTERVAL 2 HOUR)";
        
// Параметры: класс (для подзапроса), дата (для подзапроса), класс (для JOIN), дата (для фильтра 2х часов)
$params = [$class, $departure, $class, $departure];
$types = 'ssss';

// Фильтр "Откуда"
if ($from) {
    $sql .= " AND f.from_city LIKE ?";
    $params[] = '%' . $from . '%';
    $types .= 's';
}

// Фильтр "Куда"
if ($to) {
    $sql .= " AND f.to_city LIKE ?";
    $params[] = '%' . $to . '%';
    $types .= 's';
}

// Фильтр "Авиакомпания"
if ($airline !== 'all') {
    $sql .= " AND f.airline = ?";
    $params[] = $airline;
    $types .= 's';
}

// Фильтр "Цена"
if ($price_range !== 'all') {
    if ($price_range === '50000+') {
        $sql .= " AND (f.price * fc.coefficient) >= 50000";
    } else {
        list($min, $max) = explode('-', $price_range);
        $sql .= " AND (f.price * fc.coefficient) BETWEEN ? AND ?";
        $params[] = (float)$min;
        $params[] = (float)$max;
        $types .= 'dd';
    }
}

// Сортировка
switch ($sort) {
    case 'price_desc': $sql .= " ORDER BY final_price DESC"; break;
    case 'duration': $sql .= " ORDER BY f.duration_min ASC"; break;
    case 'departure': $sql .= " ORDER BY f.departure_time ASC"; break;
    default: $sql .= " ORDER BY final_price ASC"; break;
}

// ПАГИНАЦИЯ
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Сначала считаем общее количество для пагинации
$sql_count = "SELECT COUNT(*) as total FROM flights f JOIN fare_classes fc ON fc.code = ? WHERE 1=1
    AND STR_TO_DATE(CONCAT(?, ' ', f.departure_time), '%Y-%m-%d %H:%i') > DATE_ADD(NOW(), INTERVAL 2 HOUR)";
$params_count = [$class, $departure];
$types_count = 'ss';
// Добавляем остальные фильтры в $sql_count (дублируем логику, но без сортировки)
if ($from) { $sql_count .= " AND f.from_city LIKE ?"; $params_count[] = '%' . $from . '%'; $types_count .= 's'; }
if ($to) { $sql_count .= " AND f.to_city LIKE ?"; $params_count[] = '%' . $to . '%'; $types_count .= 's'; }
if ($airline !== 'all') { $sql_count .= " AND f.airline = ?"; $params_count[] = $airline; $types_count .= 's'; }
if ($price_range !== 'all' && $price_range !== '50000+') {
    list($min, $max) = explode('-', $price_range);
    $sql_count .= " AND (f.price * fc.coefficient) BETWEEN ? AND ?";
    $params_count[] = (float)$min; $params_count[] = (float)$max; $types_count .= 'dd';
} elseif ($price_range === '50000+') {
    $sql_count .= " AND (f.price * fc.coefficient) >= 50000";
}

$stmt_count = mysqli_prepare($connect, $sql_count);
mysqli_stmt_bind_param($stmt_count, $types_count, ...$params_count);
mysqli_stmt_execute($stmt_count);
$total_flights = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))['total'];
$total_pages = ceil($total_flights / $limit);
mysqli_stmt_close($stmt_count);

// Добавляем LIMIT к основному запросу
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$flights = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

$limits = ['economy' => 50, 'business' => 20, 'first' => 5];
?>