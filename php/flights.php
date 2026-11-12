<?php
// 1. Запуск сессии (строго до вывода HTML)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Подключение к БД
require_once 'db.php';

// 3. Определяем текущую страницу для подсветки меню
$current_page = basename($_SERVER['PHP_SELF']);

// 4. Получаем параметры поиска и фильтров
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$departure = $_GET['departure'] ?? '';
$class = $_GET['class'] ?? 'economy';
$sort = $_GET['sort'] ?? 'price_asc';
$airline = $_GET['airlines'] ?? 'all';
$price_range = $_GET['price_range'] ?? 'all';

// 5. Формируем базовый SQL
$sql = "SELECT f.*, (f.price * fc.coefficient) AS final_price, fc.name AS class_name
        FROM flights f
        JOIN fare_classes fc ON fc.code = ?
        WHERE 1=1";
$params = [$class];
$types = 's';

// 6. Фильтрация
if ($from) {
    $sql .= " AND f.from_city = ?";
    $params[] = $from;
    $types .= 's';
}
if ($to) {
    $sql .= " AND f.to_city = ?";
    $params[] = $to;
    $types .= 's';
}
if ($airline !== 'all') {
    $sql .= " AND f.airline = ?";
    $params[] = $airline;
    $types .= 's';
}

if ($price_range !== 'all') {
    if ($price_range === '50000+') {
        $sql .= " AND (f.price * fc.coefficient) >= 50000";
    } else {
        list($min, $max) = explode('-', $price_range);
        $sql .= " AND (f.price * fc.coefficient) BETWEEN ? AND ?";
        $params[] = $min;
        $params[] = $max;
        $types .= 'dd';
    }
}

// 7. Сортировка
switch ($sort) {
    case 'price_desc':
        $sql .= " ORDER BY final_price DESC";
        break;
    case 'duration':
        $sql .= " ORDER BY f.duration_min ASC";
        break;
    case 'departure':
        $sql .= " ORDER BY f.departure_time ASC";
        break;
    default:
        $sql .= " ORDER BY final_price ASC";
        break;
}

// 8. Выполняем запрос
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$flights = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>