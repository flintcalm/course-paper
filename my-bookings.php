<?php
// Подключаем логику
require_once 'php/my-bookings.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои бронирования - AirClick</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="img/logo-plane.png" alt="AirClick" class="logo-img">
            <span>Air</span><span>Click</span>
        </a>
        <nav>
            <ul>
                <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Главная</a></li>
                <li><a href="flights.php" class="<?= $current_page == 'flights.php' ? 'active' : '' ?>">Рейсы</a></li>
                
                <!-- Показываем эти пункты ТОЛЬКО если пользователь авторизован -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="booking.php" class="<?= $current_page == 'booking.php' ? 'active' : '' ?>">Бронирование</a></li>
                    <li><a href="my-bookings.php" class="<?= $current_page == 'my-bookings.php' ? 'active' : '' ?>">Мои бронирования</a></li>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="admin.php" class="<?= $current_page == 'admin.php' ? 'active' : '' ?>" style="color: #ff4081; font-weight: bold;">Админ-панель</a></li>
                <?php endif; ?>
                
                <li><a href="about.php" class="<?= $current_page == 'about.php' ? 'active' : '' ?>">О компании</a></li>
                <li><a href="contacts.php" class="<?= $current_page == 'contacts.php' ? 'active' : '' ?>">Контакты</a></li>
                
                <!-- Блок авторизации -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="php/logout.php">Выход (<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>)</a></li>
                <?php else: ?>
                    <li><a href="authy.php" class="<?= $current_page == 'authy.php' ? 'active' : '' ?>">Вход</a></li>
                    <li><a href="reg.php" class="<?= $current_page == 'reg.php' ? 'active' : '' ?>">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h1 class="page-title">Мои бронирования</h1>

        <!-- Если бронирований нет вообще -->
        <?php if (empty($active_bookings) && empty($past_bookings)): ?>
            <div class="card" style="text-align: center; padding: 40px;">
                <h2>У вас пока нет бронирований</h2>
                <p style="margin-bottom: 20px; color: #666;">Самое время отправиться в путешествие!</p>
                <a href="index.php" class="btn">Найти рейсы</a>
            </div>
        <?php endif; ?>

        <!-- АКТИВНЫЕ БРОНИРОВАНИЯ -->
        <?php if (!empty($active_bookings)): ?>
            <h2 class="bookings-section-title">Активные билеты</h2>
            <?php foreach ($active_bookings as $b):
                // Определяем цвет и текст статуса
                $statusClass = ($b['status'] === 'confirmed') ? 'status-confirmed' : 'status-pending';
                $statusText = ($b['status'] === 'confirmed') ? 'Подтверждено' : 'Ожидает оплаты';
                $totalPrice = $b['base_price'] * $b['passenger_count'];
            ?>
                <div class="booking-card">
                    <div class="booking-header" style="background-color: #f5f7fa; border-bottom: 1px solid #e0e0e0;">
                        <div>
                            <strong>Код брони: <?= htmlspecialchars($b['code']) ?></strong>
                        </div>
                        <div class="booking-status <?= $statusClass ?>">
                            <?= $statusText ?>
                        </div>
                    </div>
                    <div class="booking-body">
                        <div class="booking-flight-info">
                            <div class="booking-route">
                                <?= htmlspecialchars($b['from_city']) ?> — <?= htmlspecialchars($b['to_city']) ?>
                                <span class="booking-date"><?= date('d.m.Y', strtotime($b['departure_date'])) ?></span>
                            </div>
                            
                            <div class="booking-flight-row" style="margin-top: 15px;">
                                <div>
                                    <div class="booking-time"><?= htmlspecialchars($b['departure_time']) ?></div>
                                </div>
                                <div style="text-align: center;">
                                    <div class="booking-duration"><?= htmlspecialchars($b['duration']) ?></div>
                                    <div class="booking-stops"><?= $b['stops'] ?: 'Прямой рейс' ?></div>
                                </div>
                                <div>
                                    <div class="booking-time"><?= htmlspecialchars($b['arrival_time']) ?></div>
                                </div>
                            </div>
                            
                            <div class="booking-meta" style="margin-top: 15px; font-size: 14px; color: #666;">
                                Рейс: <?= htmlspecialchars($b['airline']) ?> (<?= htmlspecialchars($b['flight_number']) ?>) •
                                Класс: <?= htmlspecialchars($b['class_name']) ?> •
                                Пассажиров: <?= $b['passenger_count'] ?>
                            </div>
                        </div>
                        <div class="booking-actions">
                            <div class="booking-total-price">
                                <?= number_format($totalPrice, 0, '', ' ') ?> ₽
                            </div>
                            <button class="btn btn-details" onclick="alert('Детали бронирования в разработке')">Детали</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- ПРОШЕДШИЕ / ОТМЕНЕННЫЕ БРОНИРОВАНИЯ -->
        <?php if (!empty($past_bookings)): ?>
            <h2 class="bookings-section-title bookings-section-title-past">История поездок</h2>
            <?php foreach ($past_bookings as $b):
                // В истории могут быть отмененные или завершенные
                $statusClass = ($b['status'] === 'cancelled') ? 'status-pending' : ''; // Используем оранжевый для отмены
                $statusText = ($b['status'] === 'cancelled') ? 'Отменено' : 'Завершено';
                $totalPrice = $b['base_price'] * $b['passenger_count'];
            ?>
                <div class="booking-card booking-card-past">
                    <div class="booking-header" style="background-color: #f5f7fa; border-bottom: 1px solid #e0e0e0;">
                        <div>
                            <strong>Код брони: <?= htmlspecialchars($b['code']) ?></strong>
                        </div>
                        <div class="booking-status <?= $statusClass ?>" style="<?= $b['status'] === 'completed' ? 'background:#e0e0e0; color:#666;' : '' ?>">
                            <?= $statusText ?>
                        </div>
                    </div>
                    <div class="booking-body">
                        <div class="booking-flight-info">
                            <div class="booking-route">
                                <?= htmlspecialchars($b['from_city']) ?> — <?= htmlspecialchars($b['to_city']) ?>
                                <span class="booking-date"><?= date('d.m.Y', strtotime($b['departure_date'])) ?></span>
                            </div>
                            <div class="booking-meta" style="margin-top: 10px; font-size: 14px; color: #666;">
                                Рейс: <?= htmlspecialchars($b['airline']) ?> •
                                Пассажиров: <?= $b['passenger_count'] ?>
                            </div>
                        </div>
                        <div class="booking-actions">
                            <div class="booking-total-price" style="color: #666;">
                                <?= number_format($totalPrice, 0, '', ' ') ?> ₽
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer-section">
            <h3>AirClick</h3>
            <p>Лидер в области онлайн-бронирования авиабилетов с 2013 года. Мы делаем путешествия доступными для каждого.</p>
        </div>
        <div class="footer-section">
            <h3>Навигация</h3>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="flights.php">Рейсы</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="my-bookings.php">Мои бронирования</a></li>
                <?php endif; ?>
                <li><a href="about.php">О компании</a></li>
                <li><a href="contacts.php">Контакты</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Контакты</h3>
            <div class="">
                <div class="contact-info">
                    <span><img src="img/icon-phone.png" alt="Телефон" class="footer-icon"></span>
                    +7 (495) 123-45-67
                </div>
                <div class="contact-info">
                    <span><img src="img/icon-email.png" alt="Email" class="footer-icon"></span>
                    info@airclick.ru
                </div>
                <div class="contact-info">
                    <span><img src="img/icon-location.png" alt="Адрес" class="footer-icon"></span>
                    Москва, ул. Авиаторов, 15
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2013-2026 AirClick. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>