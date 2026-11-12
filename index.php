<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirClick - Бронирование авиабилетов</title>
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
        <section class="hero">
            <h1>Путешествуйте с комфортом</h1>
            <p>AirClick предлагает лучшие цены на авиабилеты по всему миру. Быстрое бронирование, удобный поиск и надёжное обслуживание с 2013 года.</p>

            <div class="search-form">
                <h2 class="search-title">Найти рейсы</h2>
                <form action="flights.php" method="GET">
                    <div class="search-row">
                        <div class="form-group">
                            <label for="from">Откуда</label>
                            <select id="from" name="from">
                                <option value="">Выберите город вылета</option>
                                <option value="Москва">Москва</option>
                                <option value="Санкт-Петербург">Санкт-Петербург</option>
                                <option value="Сочи">Сочи</option>
                                <option value="Париж">Париж</option>
                                <option value="Нью-Йорк">Нью-Йорк</option>
                                <option value="Токио">Токио</option>
                                <option value="Дубай">Дубай</option>
                                <option value="Лондон">Лондон</option>
                                <option value="Рим">Рим</option>
                                <option value="Пекин">Пекин</option>
                                <option value="Стамбул">Стамбул</option>
                                <option value="Бангкок">Бангкок</option>
                                <option value="Анталья">Анталья</option>
                                <option value="Казань">Казань</option>
                                <option value="Екатеринбург">Екатеринбург</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="to">Куда</label>
                            <select id="to" name="to">
                                <option value="">Выберите город назначения</option>
                                <option value="Москва">Москва</option>
                                <option value="Санкт-Петербург">Санкт-Петербург</option>
                                <option value="Сочи">Сочи</option>
                                <option value="Париж">Париж</option>
                                <option value="Нью-Йорк">Нью-Йорк</option>
                                <option value="Токио">Токио</option>
                                <option value="Дубай">Дубай</option>
                                <option value="Лондон">Лондон</option>
                                <option value="Рим">Рим</option>
                                <option value="Пекин">Пекин</option>
                                <option value="Стамбул">Стамбул</option>
                                <option value="Бангкок">Бангкок</option>
                                <option value="Анталья">Анталья</option>
                                <option value="Казань">Казань</option>
                                <option value="Екатеринбург">Екатеринбург</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="departure">Дата вылета</label>
                            <input type="date" id="departure" name="departure" required>
                        </div>
                    </div>
                    <div class="search-row">
                        <div class="form-group">
                            <label for="class">Класс</label>
                            <select id="class" name="class">
                                <option value="economy">Эконом</option>
                                <option value="business">Бизнес</option>
                                <option value="first">Первый класс</option>
                            </select>
                        </div>
                        <div class="form-group form-group-submit">
                            <button type="submit" class="btn btn-block">Найти рейсы</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="features">
            <div class="feature">
                <div class="feature-icon">
                    <img src="img/icon-search.png" alt="Быстрый поиск">
                </div>
                <h3>Быстрый поиск</h3>
                <p>Находите лучшие рейсы по выгодным ценам среди сотен авиакомпаний</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <img src="img/icon-shield.png" alt="Безопасная оплата">
                </div>
                <h3>Безопасная оплата</h3>
                <p>Все платежи защищены с использованием современных технологий шифрования</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <img src="img/icon-support.png" alt="Поддержка 24/7">
                </div>
                <h3>Поддержка 24/7</h3>
                <p>Наша служба поддержки готова помочь вам в любое время дня и ночи</p>
            </div>
        </section>

        <section class="popular-destinations">
            <h2 class="page-title">Популярные направления</h2>
            <div class="card">
                <div class="destinations-row">
                    <div class="destination-card">
                        <div class="destination-icon">
                            <img src="img/icon-newyork.png" alt="Нью-Йорк">
                        </div>
                        <h3>Нью-Йорк</h3>
                        <p>от 25 000 ₽</p>
                    </div>
                    <div class="destination-card">
                        <div class="destination-icon">
                            <img src="img/icon-paris.png" alt="Париж">
                        </div>
                        <h3>Париж</h3>
                        <p>от 18 000 ₽</p>
                    </div>
                    <div class="destination-card">
                        <div class="destination-icon">
                            <img src="img/icon-tokyo.png" alt="Токио">
                        </div>
                        <h3>Токио</h3>
                        <p>от 35 000 ₽</p>
                    </div>
                    <div class="destination-card">
                        <div class="destination-icon">
                            <img src="img/icon-dubai.png" alt="Дубай">
                        </div>
                        <h3>Дубай</h3>
                        <p>от 22 000 ₽</p>
                    </div>
                </div>
            </div>
        </section>
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

    <script>
        const today = new Date().toISOString().split('T')[0];
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 7);
        const nextWeek = tomorrow.toISOString().split('T')[0];
        document.getElementById('departure').value = today;
        document.getElementById('departure').min = today;
    </script>
</body>
</html>