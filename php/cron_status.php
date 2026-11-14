<?php
// Этот файл подключается к db.php, поэтому соединение уже есть
// 1. Отменяем активные брони, если до вылета < 1 часа
$sql_cancel = "UPDATE bookings b
                JOIN flights f ON b.flight_id = f.id
                SET b.status = 'cancelled'
                WHERE b.status = 'active'
                AND STR_TO_DATE(CONCAT(b.departure_date, ' ', f.departure_time), '%Y-%m-%d %H:%i') < DATE_ADD(NOW(), INTERVAL 1 HOUR)";
mysqli_query($connect, $sql_cancel);

// 2. Завершаем подтвержденные брони, если самолет уже прилетел
// (время вылета + длительность полета в минутах < текущего времени)
$sql_complete = "UPDATE bookings b
                JOIN flights f ON b.flight_id = f.id
                SET b.status = 'completed'
                WHERE b.status = 'confirmed'
                AND DATE_ADD(STR_TO_DATE(CONCAT(b.departure_date, ' ', f.departure_time), '%Y-%m-%d %H:%i'), INTERVAL f.duration_min MINUTE) < NOW()";
mysqli_query($connect, $sql_complete);
?>