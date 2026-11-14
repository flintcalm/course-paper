<?php
require_once 'php/profile.php';
$current_page = 'profile.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль - AirClick</title>
    <link rel="stylesheet" href="css/style.css">
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
                <li><a href="about.php" class="<?= $current_page == 'about.php' ? 'active' : '' ?>">О компании</a></li>
                <li><a href="contacts.php" class="<?= $current_page == 'contacts.php' ? 'active' : '' ?>">Контакты</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-dropdown">
                        <a href="#" class="nav-dropbtn">Личный кабинет ▼</a>
                        <div class="nav-dropdown-content">
                            <a href="profile.php">Профиль</a>
                            <a href="my-bookings.php">Мои бронирования</a>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <a href="admin.php" style="color: #ff4081; font-weight: bold;">Админ-панель</a>
                            <?php endif; ?>
                            <a href="php/logout.php">Выход</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="authy.php" class="<?= $current_page == 'authy.php' ? 'active' : '' ?>">Вход</a></li>
                    <li><a href="reg.php" class="<?= $current_page == 'reg.php' ? 'active' : '' ?>">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main class="authy">
        <h1 class="page-title">Личный кабинет</h1>
        
        <?php if ($success): ?><div class="booking-status status-confirmed mb-20 text-center"><?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="authy-error"><?= $error ?></div><?php endif; ?>

        <div class="card mb-20">
            <h3 class="mb-20">Контактные данные</h3>
            <p class="mb-20 text-muted">Логин: <strong><?= htmlspecialchars($user['login']) ?></strong> (<?= htmlspecialchars($user['surname'] . ' ' . $user['name']) ?>)</p>
            
            <form action="profile.php" method="POST">
                <input type="hidden" name="action" value="update_info">
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

                <div class="text-center mt-20">
                    <button type="submit" class="btn">Сохранить изменения</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h3 class="mb-20">Смена пароля</h3>
            <form action="profile.php" method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group">
                    <label>Текущий пароль *</label>
                    <input type="password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label>Новый пароль *</label>
                    <input type="password" name="new_password" required minlength="8">
                </div>
                <div class="text-center mt-20">
                    <button type="submit" class="btn btn-secondary">Изменить пароль</button>
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
</body>
</html>