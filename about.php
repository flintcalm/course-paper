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
    <title>О компании - AirClick</title>
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
        <h1 class="page-title">О компании AirClick</h1>

        <div class="card">
            <div class="about-mission">
                <div class="about-text">
                    <h2>Наша миссия</h2>
                    <p>AirClick был основан в 2013 году с целью сделать бронирование авиабилетов простым, быстрым и доступным для каждого. Мы верим, что путешествия должны быть вдохновляющими, а не сложными.</p>
                    <p>Наша миссия — предоставить клиентам лучший сервис по поиску и бронированию авиабилетов, сочетающий в себе современные технологии, выгодные цены и персонализированный подход.</p>
                </div>
                <div class="about-image">
                    <img src="img/icon-plane-large.png" alt="Самолёт" class="about-plane-img">
                </div>
            </div>

            <div class="about-section">
                <h2>Наши преимущества</h2>
                <div class="advantages-row">
                    <div class="advantage-card">
                        <div class="advantage-icon">
                            <img src="img/icon-money.png" alt="Лучшие цены">
                        </div>
                        <h3>Лучшие цены</h3>
                        <p>Мы сравниваем цены сотен авиакомпаний и находим для вас самые выгодные предложения.</p>
                    </div>
                    <div class="advantage-card">
                        <div class="advantage-icon">
                            <img src="img/icon-shield.png" alt="Безопасность">
                        </div>
                        <h3>Безопасность</h3>
                        <p>Все транзакции защищены с использованием современных технологий шифрования данных.</p>
                    </div>
                    <div class="advantage-card">
                        <div class="advantage-icon">
                            <img src="img/icon-support.png" alt="Поддержка 24/7">
                        </div>
                        <h3>Поддержка 24/7</h3>
                        <p>Наша служба поддержки работает круглосуточно, чтобы помочь вам в любой ситуации.</p>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2>Наша история</h2>
                <p>AirClick начал свою работу как небольшой стартап в 2013 году. Основатели компании, опытные путешественники, заметили, что процесс поиска и бронирования авиабилетов слишком сложен и запутан.</p>
                <p>Идея была проста: создать платформу, которая объединит все доступные рейсы в одном месте и позволит пользователям быстро находить лучшие варианты по самым выгодным ценам.</p>
                <p>Сегодня AirClick — это одна из ведущих компаний на рынке онлайн-бронирования авиабилетов в России и странах СНГ. Мы сотрудничаем с более чем 20 авиакомпаниями и обслуживаем более 1 миллиона клиентов ежегодно.</p>
            </div>

            <div class="about-section">
                <h2>Наши достижения</h2>
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-number">1M+</div>
                        <div>Довольных клиентов</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">20+</div>
                        <div>Авиакомпаний-партнёров</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">150+</div>
                        <div>Направлений полетов</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">13</div>
                        <div>Лет на рынке</div>
                    </div>
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