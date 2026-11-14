<?php
if (session_status() === PHP_SESSION_NONE) session_start();
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
            <img src="img/logo-plane.png" alt="AirClick">
            <span>Air</span><span>Click</span>
        </a>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="flights.php">Рейсы</a></li>
                <li><a href="about.php">О компании</a></li>
                <li><a href="contacts.php" class="active">Контакты</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-dropdown">
                        <a href="#">Личный кабинет ▼</a>
                        <div class="nav-dropdown-content">
                            <a href="profile.php">Профиль</a>
                            <a href="my-bookings.php">Мои бронирования</a>
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
        <h1 class="page-title">Контакты</h1>

        <div class="form-row mb-20">
            <!-- Левая часть -->
            <div class="form-col card">
                <h2 class="mb-20">Свяжитесь с нами</h2>
                
                <div class="contact-info mb-20">
                    <div style="width: 50px; height: 50px; background: #00b0ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <img src="img/i-location.png" alt="Адрес" style="width: 24px;">
                    </div>
                    <div>
                        <h3>Адрес</h3>
                        <p>Россия, Москва, ул. Авиаторов, 15</p>
                        <p class="text-muted" style="font-size: 14px;">БЦ "Аэропорт", 5 этаж, офис 512</p>
                    </div>
                </div>

                <div class="contact-info mb-20">
                    <div style="width: 50px; height: 50px; background: #00b0ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <img src="img/i-phone.png" alt="Телефон" style="width: 24px;">
                    </div>
                    <div>
                        <h3>Телефоны</h3>
                        <p>+7 (495) 123-45-67 — общий</p>
                        <p>+7 (800) 100-10-10 — бесплатный по России</p>
                        <p class="text-muted" style="font-size: 14px;">Пн-Пт: 9:00-21:00, Сб-Вс: 10:00-18:00</p>
                    </div>
                </div>

                <div class="contact-info mb-20">
                    <div style="width: 50px; height: 50px; background: #00b0ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <img src="img/i-email.png" alt="Email" style="width: 24px;">
                    </div>
                    <div>
                        <h3>Электронная почта</h3>
                        <p>info@airclick.ru — общие вопросы</p>
                        <p>support@airclick.ru — служба поддержки</p>
                        <p>partners@airclick.ru — для партнёров</p>
                    </div>
                </div>

                <div class="contact-info">
                    <div style="width: 50px; height: 50px; background: #00b0ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <img src="img/icon-time.png" alt="Время" style="width: 24px;">
                    </div>
                    <div>
                        <h3>Время работы</h3>
                        <p>Служба поддержки: 24/7</p>
                        <p>Офис: Пн-Пт 9:00-20:00, Сб 10:00-18:00</p>
                        <p class="text-muted" style="font-size: 14px;">Воскресенье — выходной</p>
                    </div>
                </div>
            </div>

            <!-- Правая часть -->
            <div class="form-col card">
                <h2 class="mb-20">Написать нам</h2>
                <form action="php/send_message.php" method="POST">
                    <div class="form-group">
                        <label>Ваше имя *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Ваш email *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Телефон</label>
                        <input type="tel" name="phone">
                    </div>
                    <div class="form-group">
                        <label>Тема *</label>
                        <select name="subject" required>
                            <option value="">Выберите тему</option>
                            <option value="booking">Бронирование билетов</option>
                            <option value="cancel">Отмена/возврат билета</option>
                            <option value="change">Изменение бронирования</option>
                            <option value="technical">Технические проблемы</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Сообщение *</label>
                        <textarea name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-block">Отправить сообщение</button>
                </form>
            </div>
        </div>

        <!-- Карта -->
        <div style="width: 100%; height: 400px; border-radius: 8px; overflow: hidden; margin-bottom: 30px;">
            <iframe src="https://yandex.ru/map-widget/v1/?ll=37.416071%2C55.639075&z=17&pt=37.416071,55.639075,pm2rdm" width="100%" height="100%" frameborder="0"></iframe>
        </div>

        <!-- FAQ -->
        <div class="card">
            <h2 class="mb-20">Часто задаваемые вопросы</h2>
            <div class="mb-20">
                <h3 style="color: #1a237e; margin-bottom: 10px;">Как отменить или изменить бронирование?</h3>
                <p>Вы можете отменить или изменить бронирование в разделе "Мои бронирования" на нашем сайте. Также вы можете связаться с нашей службой поддержки по телефону +7 (495) 123-45-67.</p>
            </div>
            <div class="mb-20">
                <h3 style="color: #1a237e; margin-bottom: 10px;">Сколько времени занимает возврат денег за отмененный билет?</h3>
                <p>Возврат денежных средств обычно занимает от 7 до 14 рабочих дней в зависимости от вашего банка и способа оплаты.</p>
            </div>
            <div>
                <h3 style="color: #1a237e; margin-bottom: 10px;">Нужна ли виза для перелёта в выбранную мной страну?</h3>
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