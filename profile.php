<?php
// Страница профиля пользователя и изменения личных данных.
require_once __DIR__ . '/php/profile.php';
$current_page = 'profile.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - AirClick</title>
    <script>
        (function () {
            try {
                var savedTheme = localStorage.getItem('airclick-theme');
                document.documentElement.setAttribute('data-theme', savedTheme === 'dark' ? 'dark' : 'light');
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
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
                            <a href="profile.php" class="active">Профиль</a>
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
        <button type="button" id="theme-toggle" class="theme-toggle"
                aria-label="Включить тёмную тему" aria-pressed="false" title="Тёмная тема">🌙</button>
    </header>
    
    <main class="authy profile-page">
        <div class="profile-heading">
            <h1 class="page-title">Личный кабинет</h1>
            <p class="profile-subtitle">Управляйте контактными данными и настройками безопасности аккаунта</p>
        </div>

        <?php if ($success): ?><div class="booking-status status-confirmed mb-20 text-center"><?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="authy-error"><?= $error ?></div><?php endif; ?>

        <div class="profile-summary">
            <div class="profile-avatar" aria-hidden="true">👤</div>
            <div>
                <strong><?= htmlspecialchars($user['surname'] . ' ' . $user['name']) ?></strong>
                <div class="profile-summary-text">Логин: <?= htmlspecialchars($user['login']) ?></div>
            </div>
        </div>

        <div class="profile-grid">
            <section class="card profile-panel">
                <div class="profile-panel-heading">
                    <div class="profile-panel-icon" aria-hidden="true">✉</div>
                    <div>
                        <h2>Контактные данные</h2>
                        <p>Информация для связи и оформления билетов</p>
                    </div>
                </div>

                <form action="profile.php" method="POST">
                    <input type="hidden" name="action" value="update_info">
                    <div class="profile-fields">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Телефон *</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Паспорт (серия и номер)</label>
                            <input type="text" name="passport" value="<?= htmlspecialchars($user['passport'] ?? '') ?>" placeholder="Например: 4510 123456">
                        </div>
                        <div class="form-group">
                            <label>Дата рождения</label>
                            <input type="date" name="birth_date" value="<?= htmlspecialchars($user['birth_date'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="profile-submit">
                        <button type="submit" class="btn">Сохранить изменения</button>
                    </div>
                </form>
            </section>

            <section class="card profile-panel">
                <div class="profile-panel-heading">
                    <div class="profile-panel-icon" aria-hidden="true">🔒</div>
                    <div>
                        <h2>Безопасность</h2>
                        <p>Обновление пароля аккаунта</p>
                    </div>
                </div>
                <p class="profile-password-note">Используйте пароль длиной не менее 8 символов.</p>
                <form action="profile.php" method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <div class="form-group">
                        <label>Текущий пароль *</label>
                        <input type="password" name="old_password" autocomplete="current-password" required>
                    </div>
                    <div class="form-group">
                        <label>Новый пароль *</label>
                        <input type="password" name="new_password" autocomplete="new-password" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-secondary btn-block">Изменить пароль</button>
                </form>
            </section>
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
            <div class="">
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
        </div>
        <div class="copyright">
            <p>&copy; 2013-2026 AirClick. Все права защищены.</p>
        </div>
    </footer>
    <script src="js/theme.js"></script>
</body>
</html>