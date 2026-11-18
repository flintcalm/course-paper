<?php
// Страница оформления бронирования и ввода данных пассажира.
// Подключаем всю логику
require_once __DIR__ . '/php/booking.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование - AirClick</title>
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
                                <a href="admin.php" class="text-pink">Админ-панель</a>
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
        <button type="button" id="theme-toggle" class="theme-toggle"
                aria-label="Включить тёмную тему" aria-pressed="false" title="Тёмная тема">🌙</button>
    </header>

        <main>
        <h1 class="page-title">Бронирование билета</h1>

        <?php if (isset($_SESSION['booking_error'])): ?>
            <div class="authy-error authy-error-center">
                <?= htmlspecialchars($_SESSION['booking_error']) ?>
            </div>
            <?php unset($_SESSION['booking_error']); ?>
        <?php endif; ?>
        <!-- Информация о рейсе -->
        <div class="flight-summary">
            <h3>Информация о рейсе</h3>
            <div class="flight-details">
                <div class="flight-info">
                    <p><strong>Маршрут:</strong> <?= htmlspecialchars($flight['from_city']) ?> — <?= htmlspecialchars($flight['to_city']) ?></p>
                    <p><strong>Дата:</strong> <?= date('d.m.Y', strtotime($departure_date)) ?> • <?= $flight['departure_time'] ?></p>
                    <p><strong>Рейс:</strong> <?= htmlspecialchars($flight['airline']) ?> (<?= htmlspecialchars($flight['flight_number']) ?>)</p>
                </div>
                <div class="flight-info text-right">
                    <p><strong>Класс:</strong> <span class="text-highlight"><?= htmlspecialchars($flight['class_name']) ?></span></p>
                    <p><strong>Итоговая цена:</strong> <span class="text-highlight-large"><?= number_format($flight['final_price'], 0, '', ' ') ?> ₽</span></p>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="section-title">Данные пассажиров</h3>
            
            <!-- Форма будет отправляться на будущий обработчик создания брони -->
            <form id="booking-form" action="php/booking.php" method="POST">
                
                <!-- Скрытые поля для сервера -->
                <input type="hidden" name="flight_id" value="<?= htmlspecialchars($flight['id']) ?>">
                <input type="hidden" name="class" value="<?= htmlspecialchars($class) ?>">
                <input type="hidden" name="departure_date" value="<?= htmlspecialchars($departure_date) ?>">

                <div class="passenger-form">
                    <?php
                    $defaultTicketType = 'adult';
                    if (!empty($user['birth_date'])) {
                        try {
                            $defaultAge = (new DateTime($user['birth_date']))->diff(new DateTime('today'))->y;
                            $defaultTicketType = $defaultAge < 18 ? 'child' : 'adult';
                        } catch (Throwable $e) { $defaultTicketType = 'adult'; }
                    }
                ?>
                <div class="passenger-header"><h4 id="passenger-heading">Пассажир 1 (<?= $defaultTicketType === 'child' ? 'Детский' : 'Взрослый' ?>)</h4></div>

                    <div class="form-group ticket-type-group">
                        <label for="ticket-type">Тип билета *</label>
                        <select name="ticket_type" id="ticket-type" required>
                            <option value="adult" <?= $defaultTicketType === 'adult' ? 'selected' : '' ?>>Взрослый (18 лет и старше)</option>
                            <option value="child" <?= $defaultTicketType === 'child' ? 'selected' : '' ?>>Детский (младше 18 лет)</option>
                        </select>
                        <small class="field-hint" id="ticket-age-hint">Для взрослого билета пассажиру должно быть 18 лет или больше.</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group form-col">
                            <label>Фамилия *</label>
                            <!-- value подтягивает данные из БД. Если их нет — показывается placeholder -->
                            <input type="text" name="surname" value="<?= htmlspecialchars($user['surname'] ?? '') ?>" placeholder="Фамилия" required>
                        </div>
                        <div class="form-group form-col">
                            <label>Имя *</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" placeholder="Имя" required>
                        </div>
                        <div class="form-group form-col">
                            <label>Отчество</label>
                            <input type="text" name="patronymic" value="<?= htmlspecialchars($user['patronymic'] ?? '') ?>" placeholder="Отчество">
                        </div>
                    </div>
                    
                    <div class="passenger-personal-row">
                        <div class="form-group">
                            <label for="birth-date">Дата рождения *</label>
                            <input type="date" id="birth-date" name="birth_date" value="<?= htmlspecialchars($user['birth_date'] ?? '') ?>" required>
                            <small class="field-error" id="birth-date-error" aria-live="polite"></small>
                        </div>
                        
                        <div class="form-group">
                            <label>Пол *</label>
                            <div class="radio-group">
                                <label class="radio-box">
                                    <!-- Проверяем, если в базе male -> ставим checked -->
                                    <input type="radio" name="gender" value="male" <?= (($user['gender'] ?? '') === 'male') ? 'checked' : '' ?> required>
                                    <span>Мужской</span>
                                </label>
                                <label class="radio-box">
                                    <input type="radio" name="gender" value="female" <?= (($user['gender'] ?? '') === 'female') ? 'checked' : '' ?> required>
                                    <span>Женский</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="passenger-document">Номер документа *</label>
                            <input type="text" id="passenger-document" name="document" value="<?= htmlspecialchars($user['passport'] ?? '') ?>" placeholder="Серия и номер паспорта" required>
                        </div>
                    </div>
                </div>

                <div class="passenger-form form-section-spaced">
                    <div class="passenger-header"><h4>Контактные данные</h4></div>
                    <div class="form-row">
                        <div class="form-group form-col">
                            <label>Email *</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="example@mail.ru" required>
                        </div>
                        <div class="form-group form-col">
                            <label>Телефон *</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+7 (___) ___-__-__" required>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="flights.php?from=<?= urlencode($flight['from_city']) ?>&to=<?= urlencode($flight['to_city']) ?>&departure=<?= urlencode($departure_date) ?>&class=<?= urlencode($class) ?>" class="btn btn-secondary">Назад к рейсам</a>
                    <button type="submit" class="btn">Продолжить →</button>
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

    <script>
        // Ограничение на выбор даты рождения (строго до сегодняшнего дня)
        const today = new Date();
        const maxDate = today.toISOString().split('T')[0];
        const birthInput = document.querySelector('input[name="birth_date"]');
        if (birthInput) {
            birthInput.max = maxDate;
        }
    </script>
    <script>
    (() => {
        const typeSelect = document.getElementById('ticket-type');
        const birthInput = document.getElementById('birth-date');
        const heading = document.getElementById('passenger-heading');
        const hint = document.getElementById('ticket-age-hint');
        const error = document.getElementById('birth-date-error');
        const form = document.getElementById('booking-form');
        if (!typeSelect || !birthInput || !form) return;

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const iso = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        const adultCutoff = new Date(today);
        adultCutoff.setFullYear(adultCutoff.getFullYear() - 18);
        const childMin = new Date(adultCutoff);
        childMin.setDate(childMin.getDate() + 1);

        function updateAgeRules() {
            const isChild = typeSelect.value === 'child';
            birthInput.max = isChild ? iso(today) : iso(adultCutoff);
            birthInput.min = isChild ? iso(childMin) : '1900-01-01';
            if (heading) heading.textContent = `Пассажир 1 (${isChild ? 'Детский' : 'Взрослый'})`;
            if (hint) hint.textContent = isChild
                ? 'Для детского билета пассажиру должно быть меньше 18 лет.'
                : 'Для взрослого билета пассажиру должно быть 18 лет или больше.';
            validateBirthDate();
        }

        function validateBirthDate() {
            const value = birthInput.value;
            const invalid = value && (value < birthInput.min || value > birthInput.max);
            birthInput.setCustomValidity(invalid
                ? (typeSelect.value === 'child'
                    ? 'Для детского билета возраст должен быть меньше 18 лет.'
                    : 'Для взрослого билета возраст должен быть не меньше 18 лет.')
                : '');
            if (error) error.textContent = invalid ? birthInput.validationMessage : '';
            return !invalid;
        }

        typeSelect.addEventListener('change', updateAgeRules);
        birthInput.addEventListener('change', validateBirthDate);
        form.addEventListener('submit', event => {
            if (!validateBirthDate()) {
                event.preventDefault();
                birthInput.reportValidity();
            }
        });
        updateAgeRules();
    })();
    </script>
    <script src="js/theme.js"></script>
</body>
</html>