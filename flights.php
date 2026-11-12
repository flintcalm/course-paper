<?php
// Подключаем всю логику
require_once 'php/flights.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рейсы - AirClick</title>
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
        <h1 class="page-title">Доступные рейсы</h1>

        <?php if ($from || $to): ?>
            <p style="margin-bottom: 20px; font-size: 18px;">
                Результаты: <strong><?= htmlspecialchars($from ?: 'Любой') ?></strong> — <strong><?= htmlspecialchars($to ?: 'Любой') ?></strong>
            </p>
        <?php endif; ?>

        <!-- Фильтры -->
        <form method="GET" action="flights.php" class="card">
            <!-- Скрытые поля для сохранения поиска -->
            <input type="hidden" name="from" value="<?= htmlspecialchars($from) ?>">
            <input type="hidden" name="to" value="<?= htmlspecialchars($to) ?>">
            <input type="hidden" name="departure" value="<?= htmlspecialchars($departure) ?>">
            <input type="hidden" name="class" value="<?= htmlspecialchars($class) ?>">

            <h3 class="filter-title">Фильтры поиска</h3>
            <div class="filters-row">
                <div class="filter-group">
                    <label>Сортировка:</label>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Цена (по возрастанию)</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Цена (по убыванию)</option>
                        <option value="duration" <?= $sort === 'duration' ? 'selected' : '' ?>>Длительность</option>
                        <option value="departure" <?= $sort === 'departure' ? 'selected' : '' ?>>Время вылета</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Авиакомпании:</label>
                    <select name="airlines" onchange="this.form.submit()">
                        <option value="all" <?= $airline === 'all' ? 'selected' : '' ?>>Все</option>
                        <option value="Аэрофлот" <?= $airline === 'Аэрофлот' ? 'selected' : '' ?>>Аэрофлот</option>
                        <option value="S7 Airlines" <?= $airline === 'S7 Airlines' ? 'selected' : '' ?>>S7 Airlines</option>
                        <option value="Lufthansa" <?= $airline === 'Lufthansa' ? 'selected' : '' ?>>Lufthansa</option>
                        <option value="Emirates" <?= $airline === 'Emirates' ? 'selected' : '' ?>>Emirates</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Ценовой диапазон:</label>
                    <select name="price_range" onchange="this.form.submit()">
                        <option value="all" <?= $price_range === 'all' ? 'selected' : '' ?>>Любая</option>
                        <option value="0-15000" <?= $price_range === '0-15000' ? 'selected' : '' ?>>До 15 000 ₽</option>
                        <option value="15000-30000" <?= $price_range === '15000-30000' ? 'selected' : '' ?>>15 000 - 30 000 ₽</option>
                        <option value="30000-50000" <?= $price_range === '30000-50000' ? 'selected' : '' ?>>30 000 - 50 000 ₽</option>
                        <option value="50000+" <?= $price_range === '50000+' ? 'selected' : '' ?>>Более 50 000 ₽</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Вывод рейсов -->
        <div class="flight-list">
            <?php if (empty($flights)): ?>
                <div class="no-results card">
                    <h2>Рейсы не найдены</h2>
                    <p>Попробуйте изменить параметры поиска.</p>
                    <!-- Исправлена ссылка на index.php -->
                    <a href="index.php" class="btn" style="margin-top: 15px;">На главную</a>
                </div>
            <?php else: ?>
                <?php foreach ($flights as $f): ?>
                    <div class="flight-card">
                        <div class="flight-info-section">
                            <div class="flight-time">
                                <div class="flight-time-value"><?= $f['departure_time'] ?></div>
                                <div class="flight-middle">
                                    <div class="flight-duration"><?= $f['duration'] ?></div>
                                    <div class="flight-stops-note"><?= $f['stops'] ?: 'Прямой рейс' ?></div>
                                </div>
                                <div class="flight-time-value flight-arrival"><?= $f['arrival_time'] ?></div>
                            </div>
                            <div class="flight-route">
                                <div class="flight-city"><?= htmlspecialchars($f['from_city']) ?></div>
                                <div class="flight-city"><?= htmlspecialchars($f['to_city']) ?></div>
                            </div>
                            <div class="flight-route" style="margin-top: 15px; font-size: 14px; color: #666;">
                                <div><?= htmlspecialchars($f['airline']) ?> • <?= htmlspecialchars($f['flight_number']) ?></div>
                                <div>Класс: <?= htmlspecialchars($f['class_name']) ?></div>
                            </div>
                        </div>
                        <div class="flight-price-section">
                            <div class="price"><?= number_format($f['final_price'], 0, '', ' ') ?> ₽</div>
                            <!-- Если пользователь не авторизован, при клике на "Выбрать" booking.php перенаправит его на вход -->
                            <a href="booking.php?flight_id=<?= $f['id'] ?>&class=<?= urlencode($class) ?>&departure=<?= urlencode($departure) ?>" class="btn">Выбрать</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
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
        <div class="copyright">
            <p>&copy; 2013-2026 AirClick. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>