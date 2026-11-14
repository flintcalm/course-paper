<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О компании - AirClick</title>
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
                <li><a href="about.php" class="active">О компании</a></li>
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
        <h1 class="page-title">О компании AirClick</h1>

        <div class="card">
            <div class="form-row mb-20 align-center">
                <div class="form-col">
                    <h2 class="mb-10">Наша миссия</h2>
                    <p class="mb-10">AirClick был основан в 2013 году с целью сделать бронирование авиабилетов простым, быстрым и доступным для каждого. Мы верим, что путешествия должны быть вдохновляющими, а не сложными.</p>
                    <p>Наша миссия — предоставить клиентам лучший сервис по поиску и бронированию авиабилетов, сочетающий в себе современные технологии, выгодные цены и персонализированный подход.</p>
                </div>
                <div class="form-col text-center">
                    <img src="img/icon-plane-large.png" alt="Самолёт" class="about-plane-img">
                </div>
            </div>

            <h2 class="mb-20">Наши преимущества</h2>
            <div class="form-row">
                <div class="form-col advantage-card">
                    <img src="img/icon-money.png" alt="Цены" class="advantage-icon-img">
                    <h3 class="mb-10">Лучшие цены</h3>
                    <p class="text-muted">Мы сравниваем цены сотен авиакомпаний и находим для вас самые выгодные предложения.</p>
                </div>
                <div class="form-col advantage-card">
                    <img src="img/icon-shield.png" alt="Безопасность" class="advantage-icon-img">
                    <h3 class="mb-10">Безопасность</h3>
                    <p class="text-muted">Все транзакции защищены с использованием современных технологий шифрования данных.</p>
                </div>
                <div class="form-col advantage-card">
                    <img src="img/icon-support.png" alt="Поддержка" class="advantage-icon-img">
                    <h3 class="mb-10">Поддержка 24/7</h3>
                    <p class="text-muted">Наша служба поддержки работает круглосуточно, чтобы помочь вам в любой ситуации.</p>
                </div>
            </div>

            <h2 class="mb-10 mt-20">Наша история</h2>
            <p class="mb-10">AirClick начал свою работу как небольшой стартап в 2013 году. Основатели компании, опытные путешественники, заметили, что процесс поиска и бронирования авиабилетов слишком сложен и запутан.</p>
            <p class="mb-10">Идея была проста: создать платформу, которая объединит все доступные рейсы в одном месте и позволит пользователям быстро находить лучшие варианты по самым выгодным ценам.</p>
            <p>Сегодня AirClick — это одна из ведущих компаний на рынке онлайн-бронирования авиабилетов в России и странах СНГ. Мы сотрудничаем с более чем 20 авиакомпаниями и обслуживаем более 1 миллиона клиентов ежегодно.</p>

            <h2 class="mb-20 mt-20">Наши достижения</h2>
            <div class="form-row text-center">
                <div class="form-col"><div class="price">1M+</div><p>Довольных клиентов</p></div>
                <div class="form-col"><div class="price">20+</div><p>Авиакомпаний-партнёров</p></div>
                <div class="form-col"><div class="price">150+</div><p>Направлений полетов</p></div>
                <div class="form-col"><div class="price">13</div><p>Лет на рынке</p></div>
            </div>
        </div>

        <!-- СЛАЙДЕР ДЛЯ О КОМПАНИИ -->
        <div class="slider-wrapper">
            <input type="radio" name="slider" id="s1" checked>
            <input type="radio" name="slider" id="s2">
            <input type="radio" name="slider" id="s3">
            <input type="radio" name="slider" id="s4">
            <input type="radio" name="slider" id="s5">

            <div class="slider-track">
                <div class="slide slide-6">
                    <div class="slide-content">
                        <h2>Наша миссия</h2>
                        <p>Делаем путешествия простыми, доступными и вдохновляющими для каждого.</p>
                    </div>
                </div>
                <div class="slide slide-7">
                    <div class="slide-content">
                        <h2>Наши ценности</h2>
                        <p>Безопасность, комфорт и честные цены — превыше всего.</p>
                    </div>
                </div>
                <div class="slide slide-8">
                    <div class="slide-content">
                        <h2>Наш флот</h2>
                        <p>Сотрудничаем только с проверенными авиакомпаниями и современными лайнерами.</p>
                    </div>
                </div>
                <div class="slide slide-9">
                    <div class="slide-content">
                        <h2>Наши партнеры</h2>
                        <p>Более 20 ведущих авиакомпаний мира доверяют нам своих пассажиров.</p>
                    </div>
                </div>
                <div class="slide slide-10">
                    <div class="slide-content">
                        <h2>Наше будущее</h2>
                        <p>Постоянно внедряем новые технологии для вашего максимального удобства.</p>
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
            <div class="contact-info"><img src="img/icon-phone.png" class="footer-icon" alt="Тел"> +7 (495) 123-45-67</div>
            <div class="contact-info"><img src="img/icon-email.png" class="footer-icon" alt="Email"> info@airclick.ru</div>
            <div class="contact-info"><img src="img/icon-location.png" class="footer-icon" alt="Адрес"> Москва, ул. Авиаторов, 15</div>
        </div>
        <div class="copyright">&copy; 2013-2026 AirClick. Все права защищены.</div>
    </footer>
</body>
</html>