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
    <title>Контакты - AirClick</title>
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
        <h1 class="page-title">Контакты</h1>

        <div class="contact-info">
            <div class="contact-details card">
                <h2 class="contact-title">Свяжитесь с нами</h2>

                <div class="contact-item">
                    <div class="contact-icon">
                        <img src="img/i-location.png" alt="Адрес">
                    </div>
                    <div>
                        <h3>Адрес</h3>
                        <p>Россия, Москва, ул. Авиаторов, 15</p>
                        <p class="contact-note">БЦ "Аэропорт", 5 этаж, офис 512</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <img src="img/i-phone.png" alt="Телефон">
                    </div>
                    <div>
                        <h3>Телефоны</h3>
                        <p>+7 (495) 123-45-67 — общий</p>
                        <p>+7 (800) 100-10-10 — бесплатный по России</p>
                        <p class="contact-note">Пн-Пт: 9:00-21:00, Сб-Вс: 10:00-18:00</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <img src="img/i-email.png" alt="Email">
                    </div>
                    <div>
                        <h3>Электронная почта</h3>
                        <p>info@airclick.ru — общие вопросы</p>
                        <p>support@airclick.ru — служба поддержки</p>
                        <p>partners@airclick.ru — для партнёров</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <img src="img/icon-time.png" alt="Время работы">
                    </div>
                    <div>
                        <h3>Время работы</h3>
                        <p>Служба поддержки: 24/7</p>
                        <p>Офис: Пн-Пт 9:00-20:00, Сб 10:00-18:00</p>
                        <p class="contact-note">Воскресенье — выходной</p>
                    </div>
                </div>
            </div>

            <div class="contact-form card">
                <h2 class="contact-title">Написать нам</h2>
                <form id="contact-form">
                    <div class="form-group">
                        <label for="contact-name">Ваше имя *</label>
                        <input type="text" id="contact-name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-email">Ваш email *</label>
                        <input type="email" id="contact-email" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-phone">Телефон</label>
                        <input type="tel" id="contact-phone">
                    </div>
                    <div class="form-group">
                        <label for="contact-subject">Тема *</label>
                        <select id="contact-subject" required>
                            <option value="">Выберите тему</option>
                            <option value="booking">Бронирование билетов</option>
                            <option value="cancel">Отмена/возврат билета</option>
                            <option value="change">Изменение бронирования</option>
                            <option value="technical">Технические проблемы</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contact-message">Сообщение *</label>
                        <textarea id="contact-message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-block">Отправить сообщение</button>
                </form>
            </div>
        </div>

        <div class="map-container">
            <iframe src="https://yandex.ru/map-widget/v1/?ll=37.416071%2C55.639075&z=17&pt=37.416071,55.639075,pm2rdm"
            width="100%" height="400" frameborder="0"></iframe>
        </div>

        <div class="card card-spaced">
            <h2 class="faq-title">Часто задаваемые вопросы</h2>
            <div class="faq-item">
                <h3 class="faq-question">Как отменить или изменить бронирование?</h3>
                <p>Вы можете отменить или изменить бронирование в разделе "Мои бронирования" на нашем сайте. Также вы можете связаться с нашей службой поддержки по телефону +7 (495) 123-45-67.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">Сколько времени занимает возврат денег за отмененный билет?</h3>
                <p>Возврат денежных средств обычно занимает от 7 до 14 рабочих дней в зависимости от вашего банка и способа оплаты.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">Нужна ли виза для перелёта в выбранную мной страну?</h3>
                <p>AirClick не предоставляет визовую поддержку. Пожалуйста, уточняйте информацию о необходимости визы в консульстве страны назначения.</p>
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