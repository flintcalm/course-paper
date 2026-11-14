<?php require_once 'php/flights.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск рейсов - AirClick</title>
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
                <li><a href="flights.php" class="active">Рейсы</a></li>
                <li><a href="about.php">О компании</a></li>
                <li><a href="contacts.php">Контакты</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-dropdown">
                        <a href="#">Личный кабинет ▼</a>
                        <div class="nav-dropdown-content">
                            <a href="profile.php">Профиль</a>
                            <a href="my-bookings.php">Мои бронирования</a>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <a href="admin.php" style="color: #ff4081;">Админ-панель</a>
                            <?php endif; ?>
                            <a href="php/logout.php">Выход</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="authy.php">Вход</a></li>
                    <li><a href="reg.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h1 class="page-title">Доступные рейсы</h1>

        <!-- Форма поиска -->
        <div class="search-form-box">
            <h3>Поиск рейсов</h3>
            <form action="flights.php" method="GET" class="search-grid">
                
                <!-- СКРЫТЫЕ ПОЛЯ: сохраняют текущие фильтры при новом поиске -->
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort ?? 'price_asc') ?>">
                <input type="hidden" name="airlines" value="<?= htmlspecialchars($airline ?? 'all') ?>">
                <input type="hidden" name="price_range" value="<?= htmlspecialchars($price_range ?? 'all') ?>">

                <div class="input-group">
                    <label>Откуда</label>
                    <select name="from">
                        <option value="">Выберите город вылета</option>
                        <?php
                        $cities = ['Москва', 'Санкт-Петербург', 'Сочи', 'Париж', 'Нью-Йорк', 'Токио', 'Дубай', 'Лондон', 'Рим', 'Пекин', 'Стамбул', 'Бангкок', 'Анталья', 'Казань', 'Екатеринбург'];
                        foreach ($cities as $city): ?>
                            <option value="<?= $city ?>" <?= ($from ?? '') === $city ? 'selected' : '' ?>><?= $city ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Куда</label>
                    <select name="to">
                        <option value="">Выберите город назначения</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= $city ?>" <?= ($to ?? '') === $city ? 'selected' : '' ?>><?= $city ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Дата вылета</label>
                    <input type="date" name="departure" id="depDate" value="<?= htmlspecialchars($departure ?? '') ?>" required>
                </div>
                <div class="input-group">
                    <label>Класс</label>
                    <select name="class">
                        <option value="economy" <?= ($class ?? '') === 'economy' ? 'selected' : '' ?>>Эконом</option>
                        <option value="business" <?= ($class ?? '') === 'business' ? 'selected' : '' ?>>Бизнес</option>
                        <option value="first" <?= ($class ?? '') === 'first' ? 'selected' : '' ?>>Первый класс</option>
                    </select>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-block">Найти</button>
                </div>
            </form>
        </div>

        <!-- Фильтры -->
        <div class="search-form-box">
            <h3>Фильтры поиска</h3>
            <form action="flights.php" method="GET" class="filter-grid">
                
                <!-- СКРЫТЫЕ ПОЛЯ: сохраняют параметры поиска при смене фильтра -->
                <input type="hidden" name="from" value="<?= htmlspecialchars($from ?? '') ?>">
                <input type="hidden" name="to" value="<?= htmlspecialchars($to ?? '') ?>">
                <input type="hidden" name="departure" value="<?= htmlspecialchars($departure ?? '') ?>">
                <input type="hidden" name="class" value="<?= htmlspecialchars($class ?? '') ?>">

                <div class="input-group">
                    <label>Сортировка</label>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="price_asc" <?= ($sort ?? '') === 'price_asc' ? 'selected' : '' ?>>Цена (по возрастанию)</option>
                        <option value="price_desc" <?= ($sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Цена (по убыванию)</option>
                        <option value="duration" <?= ($sort ?? '') === 'duration' ? 'selected' : '' ?>>Длительность</option>
                        <option value="departure" <?= ($sort ?? '') === 'departure' ? 'selected' : '' ?>>Время вылета</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Авиакомпании</label>
                    <select name="airlines" onchange="this.form.submit()">
                        <option value="all" <?= ($airline ?? '') === 'all' ? 'selected' : '' ?>>Все</option>
                        <option value="Аэрофлот" <?= ($airline ?? '') === 'Аэрофлот' ? 'selected' : '' ?>>Аэрофлот</option>
                        <option value="S7 Airlines" <?= ($airline ?? '') === 'S7 Airlines' ? 'selected' : '' ?>>S7 Airlines</option>
                        <option value="Lufthansa" <?= ($airline ?? '') === 'Lufthansa' ? 'selected' : '' ?>>Lufthansa</option>
                        <option value="Emirates" <?= ($airline ?? '') === 'Emirates' ? 'selected' : '' ?>>Emirates</option>
                        <option value="Уральские авиалинии" <?= ($airline ?? '') === 'Уральские авиалинии' ? 'selected' : '' ?>>Уральские авиалинии</option>
                        <option value="Turkish Airlines" <?= ($airline ?? '') === 'Turkish Airlines' ? 'selected' : '' ?>>Turkish Airlines</option>
                        <option value="Air France" <?= ($airline ?? '') === 'Air France' ? 'selected' : '' ?>>Air France</option>
                        <option value="British Airways" <?= ($airline ?? '') === 'British Airways' ? 'selected' : '' ?>>British Airways</option>
                        <option value="Победа" <?= ($airline ?? '') === 'Победа' ? 'selected' : '' ?>>Победа</option>
                        <option value="Эмирейтс" <?= ($airline ?? '') === 'Эмирейтс' ? 'selected' : '' ?>>Эмирейтс</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Ценовой диапазон</label>
                    <select name="price_range" onchange="this.form.submit()">
                        <option value="all" <?= ($price_range ?? '') === 'all' ? 'selected' : '' ?>>Любая</option>
                        <option value="0-15000" <?= ($price_range ?? '') === '0-15000' ? 'selected' : '' ?>>До 15 000 ₽</option>
                        <option value="15000-30000" <?= ($price_range ?? '') === '15000-30000' ? 'selected' : '' ?>>15 000 - 30 000 ₽</option>
                        <option value="30000-50000" <?= ($price_range ?? '') === '30000-50000' ? 'selected' : '' ?>>30 000 - 50 000 ₽</option>
                        <option value="50000+" <?= ($price_range ?? '') === '50000+' ? 'selected' : '' ?>>Более 50 000 ₽</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Список рейсов -->
        <div class="flight-list mt-20">
            <?php if (empty($flights)): ?>
                <div class="card text-center">
                    <h2>Рейсы не найдены</h2>
                </div>
            <?php else: ?>
                <?php foreach ($flights as $f): 
                    $max = $limits[$class] ?? 50;
                    $avail = max(0, $max - $f['booked_seats']);
                ?>
                    <div class="flight-card">
                        <div class="flight-card-left">
                            <div class="flight-time-row">
                                <div class="flight-time-value"><?= $f['departure_time'] ?></div>
                                <div class="flight-duration-info">
                                    <div><?= $f['duration'] ?></div>
                                    <div><?= $f['stops'] ?: 'прямой рейс' ?></div>
                                </div>
                                <div class="flight-time-value" style="text-align: right;"><?= $f['arrival_time'] ?></div>
                            </div>
                            <div class="flight-cities-row">
                                <div><?= htmlspecialchars($f['from_city']) ?></div>
                                <div style="text-align: right;"><?= htmlspecialchars($f['to_city']) ?></div>
                            </div>
                            <div class="flight-meta-row">
                                <div><?= htmlspecialchars($f['airline']) ?> • <?= htmlspecialchars($f['flight_number']) ?></div>
                                <div style="text-align: right;">Класс: <?= htmlspecialchars($f['class_name']) ?></div>
                            </div>
                        </div>
                        <div class="flight-card-right">
                            <div class="flight-price"><?= number_format($f['final_price'], 0, '', ' ') ?> ₽</div>
                            <div class="flight-seats <?= $avail > 0 ? 'green' : 'red' ?>">
                                <?= $avail > 0 ? "Осталось мест: $avail" : "Мест нет" ?>
                            </div>
                            <?php if ($avail > 0): ?>
                                <a href="booking.php?flight_id=<?= $f['id'] ?>&class=<?= urlencode($class) ?>&departure=<?= urlencode($departure) ?>" class="btn w-100">Выбрать</a>
                            <?php else: ?>
                                <button class="btn w-100" style="background: #ccc; cursor: not-allowed;" disabled>Нет мест</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- ПАГИНАЦИЯ -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php $queryParams = $_GET; ?>

                <!-- Первая страница -->
                <?php if ($page > 1): ?>
                    <?php $queryParams['page'] = 1; ?>
                    <a href="flights.php?<?= http_build_query($queryParams) ?>">«</a>
                <?php endif; ?>

                <!-- Назад (на 5 страниц или на 1, если близко к началу) -->
                <?php if ($page > 1): ?>
                    <?php $queryParams['page'] = max(1, $page - 5); ?>
                    <a href="flights.php?<?= http_build_query($queryParams) ?>">‹</a>
                <?php endif; ?>

                <!-- Окно страниц (5 назад, текущая, 5 вперед) -->
                <?php 
                $start = max(1, $page - 5);
                $end = min($total_pages, $page + 5);
                
                for ($i = $start; $i <= $end; $i++): 
                    $queryParams['page'] = $i;
                ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="flights.php?<?= http_build_query($queryParams) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Вперед (на 5 страниц) -->
                <?php if ($page < $total_pages): ?>
                    <?php $queryParams['page'] = min($total_pages, $page + 5); ?>
                    <a href="flights.php?<?= http_build_query($queryParams) ?>">›</a>
                <?php endif; ?>

                <!-- Последняя страница -->
                <?php if ($page < $total_pages): ?>
                    <?php $queryParams['page'] = $total_pages; ?>
                    <a href="flights.php?<?= http_build_query($queryParams) ?>">»</a>
                <?php endif; ?>
            </div>
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
        const dateInput = document.getElementById('depDate');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.min = today;
            if (!dateInput.value) {
                dateInput.value = today;
            }
        }
    </script>
</body>
</html>