<?php require_once 'php/my-bookings.php'; ?>
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
            <img src="img/logo-plane.png" alt="AirClick">
            <span>Air</span><span>Click</span>
        </a>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="flights.php">Рейсы</a></li>
                <li><a href="about.php">О компании</a></li>
                <li><a href="contacts.php">Контакты</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-dropdown">
                        <a href="#">Личный кабинет ▼</a>
                        <div class="nav-dropdown-content">
                            <a href="profile.php">Профиль</a>
                            <a href="my-bookings.php" class="active">Мои бронирования</a>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <a href="admin.php" style="color: #ff4081;">Админ-панель</a>
                            <?php endif; ?>
                            <a href="php/logout.php">Выход</a>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <!-- БЛОК ВЫВОДА ОШИБКИ -->
        <?php if (isset($_SESSION['action_error'])): ?>
            <div class="authy-error" style="text-align: center; margin-bottom: 20px; font-size: 16px; padding: 10px; background: #ffebee; border-radius: 4px; color: #c62828;">
                <?= htmlspecialchars($_SESSION['action_error']) ?>
            </div>
            <?php unset($_SESSION['action_error']); ?>
        <?php endif; ?>

        <h1 class="page-title">Мои бронирования</h1>

        <!-- Активные билеты -->
        <h2 class="mb-20">Активные билеты</h2>
        <?php if (empty($active_bookings)): ?>
            <div class="card">
                <p class="text-muted">У вас нет активных бронирований.</p>
            </div>
        <?php else: ?>
            <?php foreach ($active_bookings as $b): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <strong>Код брони: <?= htmlspecialchars($b['code']) ?></strong>
                        <span class="booking-status status-<?= $b['status'] ?>">
                            <?= $b['status'] == 'active' ? 'Ожидает оплаты' : 'Подтверждено' ?>
                        </span>
                    </div>
                    <div class="booking-body">
                        <div class="booking-flight-info">
                            <h3 class="mb-10">
                                <?= htmlspecialchars($b['from_city']) ?> — <?= htmlspecialchars($b['to_city']) ?>
                                <span class="text-muted" style="font-size: 16px; font-weight: normal; margin-left: 10px;">
                                    <?= date('d.m.Y', strtotime($b['departure_date'])) ?>
                                    в <?= htmlspecialchars($b['departure_time']) ?> - <?= htmlspecialchars($b['arrival_time']) ?>
                                </span>
                            </h3>
                            <p class="text-muted" style="font-size: 14px;">
                                Рейс: <?= htmlspecialchars($b['airline']) ?> • Пассажиров: <?= $b['passenger_count'] ?>
                            </p>
                        </div>
                        
                        <div class="booking-actions" style="width: 200px;">
                            <div class="price" style="margin-bottom: 15px;"><?= number_format($b['base_price'] * $b['passenger_count'], 0, '', ' ') ?> ₽</div>
                            
                            <?php if ($b['status'] == 'active'): ?>
                                <form action="php/user_booking_action.php" method="POST" class="w-100">
                                    <input type="hidden" name="action" value="confirm">
                                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                    <button type="submit" class="btn btn-pay btn-block mb-10" onclick="return confirm('Вы уверены, что хотите оплатить эту бронь?')">Оплатить</button>
                                </form>
                            <?php endif; ?>
                            
                            <form action="php/user_booking_action.php" method="POST" class="w-100">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <button type="submit" class="btn btn-secondary btn-block mb-10" onclick="return confirm('Вы уверены?')">Отменить</button>
                            </form>

                            <button class="btn btn-block" style="background: #888;" onclick="toggleDetails(<?= $b['id'] ?>, this)">Детали ▼</button>
                        </div>
                    </div>
                    
                    <div id="details-<?= $b['id'] ?>" class="booking-details-toggle">
                        <h4 class="mb-10">Пассажиры</h4>
                        <ul class="passenger-list">
                            <?php foreach ($b['passengers'] as $p): ?>
                                <li>
                                    <b><?= htmlspecialchars($p['surname'] . ' ' . $p['name'] . ' ' . $p['patronymic']) ?></b><br>
                                    Дата рождения: <?= date('d.m.Y', strtotime($p['birth_date'])) ?> • Документ: <?= htmlspecialchars($p['document']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="mt-10"><b>Контакты:</b> <?= htmlspecialchars($b['email']) ?> • <?= htmlspecialchars($b['phone']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- История -->
        <h2 class="mt-20 mb-20">История полётов</h2>
        <?php if (empty($past_bookings)): ?>
            <div class="card">
                <p class="text-muted">История пуста.</p>
            </div>
        <?php else: ?>
            <?php foreach ($past_bookings as $b): ?>
                <div class="booking-card">
                    <div class="booking-header" style="background: #fff;">
                        <strong>Код брони: <?= htmlspecialchars($b['code']) ?></strong>
                        <span class="booking-status status-<?= $b['status'] ?>">
                            <?= $b['status'] == 'cancelled' ? 'Отменено' : 'Завершено' ?>
                        </span>
                    </div>
                    <div class="booking-body text-muted">
                        <div class="booking-flight-info">
                            <h3 class="mb-10">
                                <?= htmlspecialchars($b['from_city']) ?> — <?= htmlspecialchars($b['to_city']) ?>
                                <span class="text-muted" style="font-size: 16px; font-weight: normal; margin-left: 10px;">
                                    <?= date('d.m.Y', strtotime($b['departure_date'])) ?>
                                    в <?= htmlspecialchars($b['departure_time']) ?> - <?= htmlspecialchars($b['arrival_time']) ?>
                                </span>
                            </h3>
                            <p class="text-muted" style="font-size: 14px;">
                                Рейс: <?= htmlspecialchars($b['airline']) ?> • Пассажиров: <?= $b['passenger_count'] ?>
                            </p>
                        </div>
                        
                        <div class="booking-actions" style="width: 200px;">
                            <div class="price" style="color: #666; margin-bottom: 15px;"><?= number_format($b['base_price'] * $b['passenger_count'], 0, '', ' ') ?> ₽</div>
                            
                            <?php if ($b['status'] === 'cancelled'): ?>
                                <?php
                                $dep_datetime = strtotime($b['departure_date'] . ' ' . $b['departure_time']);
                                $time_left = $dep_datetime - time();
                                // Показываем кнопку только если до вылета больше 20 минут
                                if ($time_left > 1200):
                                    // Определяем текст и цвет кнопки в зависимости от времени
                                    if ($time_left > 3600) {
                                        $btnText = 'Восстановить';
                                        $btnClass = 'btn-restore';
                                    } else {
                                        $btnText = 'Восстановить и оплатить';
                                        $btnClass = 'btn-pay'; // Зеленая кнопка
                                    }
                                ?>
                                    <form action="php/user_booking_action.php" method="POST" class="w-100">
                                        <input type="hidden" name="action" value="restore">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn <?= $btnClass ?> btn-block mb-10" onclick="return confirm('<?= $btnText ?>?')"><?= $btnText ?></button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <button class="btn btn-block" style="background: #888;" onclick="toggleDetails(<?= $b['id'] ?>, this)">Детали ▼</button>
                        </div>
                    </div>
                    
                    <div id="details-<?= $b['id'] ?>" class="booking-details-toggle">
                        <h4 class="mb-10">Пассажиры</h4>
                        <ul class="passenger-list">
                            <?php foreach ($b['passengers'] as $p): ?>
                                <li>
                                    <b><?= htmlspecialchars($p['surname'] . ' ' . $p['name'] . ' ' . $p['patronymic']) ?></b><br>
                                    Дата рождения: <?= date('d.m.Y', strtotime($p['birth_date'])) ?> • Документ: <?= htmlspecialchars($p['document']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="mt-10"><b>Контакты:</b> <?= htmlspecialchars($b['email']) ?> • <?= htmlspecialchars($b['phone']) ?></p>
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
                <li><a href="my-bookings.php">Мои бронирования</a></li>
                <li><a href="about.php">О компании</a></li>
                <li><a href="contacts.php">Контакты</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Контакты</h3>
            <div class="contact-info"><img src="img/icon-phone.png" class="footer-icon" alt="Тел"> +7 (495) 123-45-67</div>
            <div class="contact-info"><img src="img/icon-email.png" class="footer-icon" alt="Email"> info@airclick.ru</div>
            <div class="contact-info"><img src="img/icon-location.png" class="footer-icon" alt="Адрес"> Москва, ул. Авиаторов, 15</div>
        </div>
        <div class="copyright">&copy; 2013-2026 AirClick. Все права защищены.</div>
    </footer>

    <script>
        function toggleDetails(id, btn) {
            const block = document.getElementById('details-' + id);
            block.classList.toggle('open');
            if (block.classList.contains('open')) {
                btn.textContent = 'Скрыть ▲';
            } else {
                btn.textContent = 'Детали ▼';
            }
        }
    </script>
</body>
</html>