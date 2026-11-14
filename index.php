<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);

// Список всех 15 городов для выпадающих списков
$cities = ['Москва', 'Санкт-Петербург', 'Сочи', 'Париж', 'Нью-Йорк', 'Токио', 'Дубай', 'Лондон', 'Рим', 'Пекин', 'Стамбул', 'Бангкок', 'Анталья', 'Казань', 'Екатеринбург'];
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
            <img src="img/logo-plane.png" alt="AirClick">
            <span>Air</span><span>Click</span>
        </a>
        <nav>
            <ul>
                <li><a href="index.php" class="active">Главная</a></li>
                <li><a href="flights.php">Рейсы</a></li>
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
        <!-- БЛОК ПОИСКА -->
        <div class="hero-search">
            <h1>Путешествуйте с комфортом</h1>
            <p>AirClick предлагает лучшие цены на авиабилеты по всему миру. Быстрое бронирование, удобный поиск и надёжное обслуживание с 2013 года.</p>
            
            <div class="search-form-card">
                <h3>Найти рейсы</h3>
                <form action="flights.php" method="GET" class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label>Откуда</label>
                            <select name="from">
                                <option value="">Выберите город вылета</option>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?= $city ?>"><?= $city ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label>Куда</label>
                            <select name="to">
                                <option value="">Выберите город назначения</option>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?= $city ?>"><?= $city ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label>Дата вылета</label>
                            <input type="date" name="departure" id="depDate" required>
                        </div>
                    </div>
                    
                    <div class="form-col">
                        <div class="form-group">
                            <label>Класс</label>
                            <select name="class">
                                <option value="economy">Эконом</option>
                                <option value="business">Бизнес</option>
                                <option value="first">Первый класс</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col search-btn-wrapper">
                        <button type="submit" class="btn btn-block">Найти рейсы</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ПРЕИМУЩЕСТВА -->
        <div class="form-row text-center mb-20">
            <div class="form-col">
                <img src="img/icon-search.png" alt="Поиск" class="feature-icon-img">
                <h3>Быстрый поиск</h3>
                <p class="text-muted">Находите лучшие рейсы по выгодным ценам среди сотен авиакомпаний</p>
            </div>
            <div class="form-col">
                <img src="img/icon-shield.png" alt="Безопасность" class="feature-icon-img">
                <h3>Безопасная оплата</h3>
                <p class="text-muted">Все платежи защищены с использованием современных технологий шифрования</p>
            </div>
            <div class="form-col">
                <img src="img/icon-support.png" alt="Поддержка" class="feature-icon-img">
                <h3>Поддержка 24/7</h3>
                <p class="text-muted">Наша служба поддержки готова помочь вам в любое время дня и ночи</p>
            </div>
        </div>

        <!-- ПОПУЛЯРНЫЕ НАПРАВЛЕНИЯ -->
        <h2 class="page-title mt-20">Популярные направления</h2>
        <div class="card">
            <div class="form-row text-center">
                <div class="form-col">
                    <img src="img/icon-newyork.png" alt="Нью-Йорк" class="destination-icon-img">
                    <h3>Нью-Йорк</h3>
                    <p>от 25 000 ₽</p>
                </div>
                <div class="form-col">
                    <img src="img/icon-paris.png" alt="Париж" class="destination-icon-img">
                    <h3>Париж</h3>
                    <p>от 18 000 ₽</p>
                </div>
                <div class="form-col">
                    <img src="img/icon-tokyo.png" alt="Токио" class="destination-icon-img">
                    <h3>Токио</h3>
                    <p>от 35 000 ₽</p>
                </div>
                <div class="form-col">
                    <img src="img/icon-dubai.png" alt="Дубай" class="destination-icon-img">
                    <h3>Дубай</h3>
                    <p>от 22 000 ₽</p>
                </div>
            </div>
        </div>

        <!-- СЛАЙДЕР -->
        <div class="slider-wrapper">
            <input type="radio" name="slider" id="s1" checked>
            <input type="radio" name="slider" id="s2">
            <input type="radio" name="slider" id="s3">
            <input type="radio" name="slider" id="s4">
            <input type="radio" name="slider" id="s5">

            <div class="slider-track">
                <div class="slide slide-1">
                    <div class="slide-content">
                        <h2>Летайте с комфортом</h2>
                        <p>Лучшие цены на авиабилеты по всему миру. Быстрое бронирование и надёжное обслуживание с 2013 года.</p>
                        <a href="flights.php" class="btn">Найти рейсы</a>
                    </div>
                </div>
                <div class="slide slide-2">
                    <div class="slide-content">
                        <h2>Откройте новые горизонты</h2>
                        <p>Более 150 направлений полетов. Отпуск мечты начинается здесь.</p>
                        <a href="flights.php" class="btn btn-secondary">Бизнес-класс</a>
                    </div>
                </div>
                <div class="slide slide-3">
                    <div class="slide-content">
                        <h2>Мы заботимся о вас</h2>
                        <p>Поддержка 24/7 и безопасная оплата на всех этапах путешествия.</p>
                        <a href="about.php" class="btn">Узнать больше</a>
                    </div>
                </div>
                <div class="slide slide-4">
                    <div class="slide-content">
                        <h2>Удобное бронирование</h2>
                        <p>Купите билет в пару кликов прямо с телефона.</p>
                        <a href="reg.php" class="btn">Регистрация</a>
                    </div>
                </div>
                <div class="slide slide-5">
                    <div class="slide-content">
                        <h2>Надежный партнер</h2>
                        <p>Работаем с 2013 года.</p>
                        <a href="about.php" class="btn">О компании</a>
                    </div>
                </div>
            </div>

            <div class="controls">
                <label for="s5" class="slider-btn prev b1">❮</label>
                <label for="s2" class="slider-btn next b1">❯</label>
                <label for="s1" class="slider-btn prev b2">❮</label>
                <label for="s3" class="slider-btn next b2">❯</label>
                <label for="s2" class="slider-btn prev b3">❮</label>
                <label for="s4" class="slider-btn next b3">❯</label>
                <label for="s3" class="slider-btn prev b4">❮</label>
                <label for="s5" class="slider-btn next b4">❯</label>
                <label for="s4" class="slider-btn prev b5">❮</label>
                <label for="s1" class="slider-btn next b5">❯</label>

                <div class="slider-dots">
                    <label for="s1" class="dot d1"></label>
                    <label for="s2" class="dot d2"></label>
                    <label for="s3" class="dot d3"></label>
                    <label for="s4" class="dot d4"></label>
                    <label for="s5" class="dot d5"></label>
                </div>
            </div>
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
                <li><a href="my-bookings.php">Мои бронирования</a></li>
                <li><a href="about.php">О компании</a></li>
                <li><a href="contacts.php">Контакты</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Контакты</h3>
            <div class="contact-info">
                <img src="img/icon-phone.png" class="footer-icon" alt="Тел"> +7 (495) 123-45-67
            </div>
            <div class="contact-info">
                <img src="img/icon-email.png" class="footer-icon" alt="Email"> info@airclick.ru
            </div>
            <div class="contact-info">
                <img src="img/icon-location.png" class="footer-icon" alt="Адрес"> Москва, ул. Авиаторов, 15
            </div>
        </div>
        <div class="copyright">&copy; 2013-2026 AirClick. Все права защищены.</div>
    </footer>

    <script>
        // Установка минимальной даты (сегодня)
        const dateInput = document.getElementById('depDate');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.min = today;
            dateInput.value = today;
        }
    </script>
</body>
</html>