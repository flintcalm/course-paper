<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
// Получаем ошибки и старые данные, затем сразу удаляем их из сессии
$errors = $_SESSION['reg_errors'] ?? [];
$old = $_SESSION['reg_old'] ?? [];
unset($_SESSION['reg_errors'], $_SESSION['reg_old']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - AirClick</title>
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

    <main class="authy">
        <h1 class="page-title">Регистрация</h1>

        <div class="card">
            <?php if ($errors): ?>
                <div class="auth-error">
                    <?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
                </div>
            <?php endif; ?>

            <form action="php/reg.php" method="post">
                <div class="form-group">
                    <label for="reg-login">Логин *</label>
                    <input type="text" id="reg-login" name="login" value="<?= htmlspecialchars($old['login'] ?? '') ?>" placeholder="Логин (минимум 6 символов)" required>
                </div>

                <div class="form-group">
                    <label for="reg-password">Пароль *</label>
                    <input type="password" id="reg-password" name="password" placeholder="Пароль (минимум 8 символов)" minlength="8" required>
                </div>

                <div class="form-row">
                    <div class="form-group form-col">
                        <label for="reg-surname">Фамилия *</label>
                        <input type="text" id="reg-surname" name="surname" value="<?= htmlspecialchars($old['surname'] ?? '') ?>" placeholder="Фамилия" required>
                    </div>
                    <div class="form-group form-col">
                        <label for="reg-name">Имя *</label>
                        <input type="text" id="reg-name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" placeholder="Имя" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-patronymic">Отчество</label>
                    <input type="text" id="reg-patronymic" name="patronymic" value="<?= htmlspecialchars($old['patronymic'] ?? '') ?>" placeholder="Отчество">
                </div>

                <div class="form-group">
                    <label>Пол *</label>
                    <div class="radio-group">
                        <label class="radio-box">
                            <input type="radio" name="gender" value="male" <?= (($old['gender'] ?? '') === 'male') ? 'checked' : '' ?> required>
                            <span>Мужской</span>
                        </label>
                        <label class="radio-box">
                            <input type="radio" name="gender" value="female" <?= (($old['gender'] ?? '') === 'female') ? 'checked' : '' ?> required>
                            <span>Женский</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-phone">Телефон *</label>
                    <input type="tel" id="reg-phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="89999990000" pattern="8[0-9]{10}" maxlength="11" required>
                </div>

                <div class="form-group">
                    <label for="reg-email">E-mail *</label>
                    <input type="email" id="reg-email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="example@mail.ru" required>
                </div>

                <button type="submit" class="btn btn-block">Зарегистрироваться</button>

                <div class="authy-links">
                    <a href="authy.php">Уже есть аккаунт? Войти</a>
                </div>
            </form>
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