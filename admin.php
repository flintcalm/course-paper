<?php require_once 'php/admin.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель - AirClick</title>
    <link rel="stylesheet" href="css/style.css">
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
                            <a href="profile.php">Профиль</a>
                            <a href="my-bookings.php">Мои бронирования</a>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <a href="admin.php" class="text-pink active">Админ-панель</a>
                            <?php endif; ?>
                            <a href="php/logout.php">Выход</a>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h1 class="page-title">Панель администратора</h1>

        <!-- Управление бронированиями -->
        <div class="card">
            <h2 class="section-title">Управление бронированиями</h2>
            <form method="GET" action="admin.php" class="form-row align-end">
                <div class="form-col" style="flex: 3;"> <!-- Даем селекту больше места -->
                    <div class="form-group mb-0">
                        <label>Пользователь:</label>
                        <select name="user_id" required>
                            <option value="0">-- Выберите пользователя --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= $selected_user_id == $u['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['surname'] . ' ' . $u['name'] . ' (@' . $u['login'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col" style="flex: 0;">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn">Показать</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Активные и История -->
        <?php if ($selected_user_id > 0): ?>
            <h3 class="section-title">Активные бронирования</h3>
            <?php if (empty($active_bookings)): ?>
                <div class="card"><p>Нет активных бронирований.</p></div>
            <?php else: ?>
                <div class="card table-responsive">
                    <table class="admin-table">
                        <thead><tr><th>Код</th><th>Маршрут</th><th>Дата</th><th>Статус</th><th>Действия</th></tr></thead>
                        <tbody>
                            <?php foreach ($active_bookings as $b): ?>
                            <tr>
                                <td><strong><?= $b['code'] ?></strong></td>
                                <td><?= $b['from_city'] ?> — <?= $b['to_city'] ?></td>
                                <td><?= date('d.m.Y', strtotime($b['departure_date'])) ?></td>
                                <td>
                                    <form action="php/admin_action.php" method="POST">
                                        <input type="hidden" name="action" value="change_status">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                        <input type="hidden" name="current_user_id" value="<?= $selected_user_id ?>">
                                        <select name="status" class="admin-select select-<?= $b['status'] ?>" onchange="this.form.submit()">
                                            <option value="active" <?= $b['status']=='active'?'selected':'' ?>>Активно</option>
                                            <option value="confirmed" <?= $b['status']=='confirmed'?'selected':'' ?>>Подтверждено</option>
                                            <option value="cancelled" <?= $b['status']=='cancelled'?'selected':'' ?>>Отменено</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить?');">
                                        <input type="hidden" name="action" value="delete_booking">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                        <input type="hidden" name="current_user_id" value="<?= $selected_user_id ?>">
                                        <button type="submit" class="btn btn-secondary btn-sm">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <h3 class="section-title">История бронирований</h3>
            <?php if (empty($past_bookings)): ?>
                <div class="card"><p>История пуста.</p></div>
            <?php else: ?>
                <div class="card table-responsive">
                    <table class="admin-table">
                        <thead><tr><th>Код</th><th>Маршрут</th><th>Дата</th><th>Статус</th><th>Действия</th></tr></thead>
                        <tbody>
                            <?php foreach ($past_bookings as $b): ?>
                            <tr>
                                <td><strong><?= $b['code'] ?></strong></td>
                                <td><?= $b['from_city'] ?> — <?= $b['to_city'] ?></td>
                                <td><?= date('d.m.Y', strtotime($b['departure_date'])) ?></td>
                                <td>
                                    <form action="php/admin_action.php" method="POST">
                                        <input type="hidden" name="action" value="change_status">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                        <input type="hidden" name="current_user_id" value="<?= $selected_user_id ?>">
                                        <select name="status" class="admin-select select-<?= $b['status'] ?>" onchange="this.form.submit()">
                                            <option value="active" <?= $b['status']=='active'?'selected':'' ?>>Активно</option>
                                            <option value="confirmed" <?= $b['status']=='confirmed'?'selected':'' ?>>Подтверждено</option>
                                            <option value="cancelled" <?= $b['status']=='cancelled'?'selected':'' ?>>Отменено</option>
                                            <option value="completed" <?= $b['status']=='completed'?'selected':'' ?>>Завершено</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить?');">
                                        <input type="hidden" name="action" value="delete_booking">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                        <input type="hidden" name="current_user_id" value="<?= $selected_user_id ?>">
                                        <button type="submit" class="btn btn-secondary btn-sm">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Пользователи -->
        <div class="card">
            <h2 class="section-title">Зарегистрированные пользователи</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead><tr><th>ID</th><th>ФИО / Логин</th><th>Email</th><th>Роль</th><th>Действия</th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['surname'] . ' ' . $u['name']) ?><br><small>@<?= $u['login'] ?></small></td>
                            <td><?= $u['email'] ?></td>
                            <td><?= $u['role'] === 'admin' ? '<span class="text-pink">Админ</span>' : 'Юзер' ?></td>
                            <td>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить?');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-dark btn-sm">Удалить</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Сообщения -->
        <div class="card">
            <h2 class="section-title">Сообщения из формы обратной связи</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead><tr><th>Дата</th><th>Отправитель</th><th>Тема</th><th>Сообщение</th><th>Действия</th></tr></thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr><td colspan="5" class="text-center">Нет сообщений</td></tr>
                        <?php endif; ?>
                        <?php foreach ($messages as $m): ?>
                        <tr>
                            <td><?= date('d.m.Y H:i', strtotime($m['created_at'])) ?></td>
                            <td><strong><?= htmlspecialchars($m['name']) ?></strong><br><small><?= htmlspecialchars($m['email']) ?></small></td>
                            <td><?= htmlspecialchars($m['subject']) ?></td>
                            <td><?= nl2br(htmlspecialchars($m['message'])) ?></td>
                            <td>
                                <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить?');">
                                    <input type="hidden" name="action" value="delete_message">
                                    <input type="hidden" name="message_id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm">Удалить</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Форма рейса -->
        <div class="card">
            <h2 class="section-title"><?= $edit_flight ? 'Редактировать рейс' : 'Добавить новый рейс' ?></h2>
            <form action="php/admin_action.php" method="POST" class="admin-form-grid">
                <input type="hidden" name="action" value="save_flight">
                <input type="hidden" name="id" value="<?= $edit_flight['id'] ?? 0 ?>">
                
                <div class="form-group"><label>Откуда *</label><input type="text" name="from_city" value="<?= htmlspecialchars($edit_flight['from_city'] ?? '') ?>" required></div>
                <div class="form-group"><label>Куда *</label><input type="text" name="to_city" value="<?= htmlspecialchars($edit_flight['to_city'] ?? '') ?>" required></div>
                <div class="form-group"><label>Время вылета (ЧЧ:ММ) *</label><input type="text" name="departure_time" value="<?= htmlspecialchars($edit_flight['departure_time'] ?? '') ?>" placeholder="12:30" required></div>
                <div class="form-group"><label>Время прилета (ЧЧ:ММ) *</label><input type="text" name="arrival_time" value="<?= htmlspecialchars($edit_flight['arrival_time'] ?? '') ?>" placeholder="15:00" required></div>
                <div class="form-group"><label>Длительность (текст) *</label><input type="text" name="duration" value="<?= htmlspecialchars($edit_flight['duration'] ?? '') ?>" placeholder="2ч 30м" required></div>
                <div class="form-group"><label>Длительность (мин) *</label><input type="number" name="duration_min" value="<?= htmlspecialchars($edit_flight['duration_min'] ?? '') ?>" placeholder="150" required></div>
                <div class="form-group"><label>Авиакомпания *</label><input type="text" name="airline" value="<?= htmlspecialchars($edit_flight['airline'] ?? '') ?>" required></div>
                <div class="form-group"><label>Номер рейса *</label><input type="text" name="flight_number" value="<?= htmlspecialchars($edit_flight['flight_number'] ?? '') ?>" required></div>
                <div class="form-group"><label>Самолет</label><input type="text" name="aircraft" value="<?= htmlspecialchars($edit_flight['aircraft'] ?? '') ?>"></div>
                <div class="form-group"><label>Базовая цена (Эконом) *</label><input type="number" step="0.01" name="price" value="<?= htmlspecialchars($edit_flight['price'] ?? '') ?>" required></div>
                <div class="form-group"><label>Остановки</label><input type="text" name="stops" value="<?= htmlspecialchars($edit_flight['stops'] ?? 'прямой рейс') ?>"></div>
                <div class="form-group form-group-full">
                    <button type="submit" class="btn"><?= $edit_flight ? 'Обновить' : 'Добавить' ?></button>
                    <?php if ($edit_flight): ?><a href="admin.php" class="btn btn-secondary ml-10">Отмена</a><?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Таблица рейсов с фильтрами -->
        <div class="card">
            <h2 class="section-title">Рейсы</h2>
            
            <!-- Блок фильтров -->
            <form method="GET" action="admin.php" class="filters-wrapper">
                <?php if ($selected_user_id > 0): ?>
                    <input type="hidden" name="user_id" value="<?= $selected_user_id ?>">
                <?php endif; ?>
                <!-- Скрытые поля для сохранения текущей сортировки -->
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">

                <div class="form-row">
                    <div class="form-col">
                        <label class="filter-label">Откуда</label>
                        <select name="f_from" onchange="this.form.submit()">
                            <option value="">Все города</option>
                            <?php foreach ($cities as $c): ?>
                                <option value="<?= $c ?>" <?= $f_from === $c ? 'selected' : '' ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-col">
                        <label class="filter-label">Куда</label>
                        <select name="f_to" onchange="this.form.submit()">
                            <option value="">Все города</option>
                            <?php foreach ($cities as $c): ?>
                                <option value="<?= $c ?>" <?= $f_to === $c ? 'selected' : '' ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-col">
                        <label class="filter-label">Авиакомпания</label>
                        <select name="f_airline" onchange="this.form.submit()">
                            <option value="all">Все</option>
                            <option value="Аэрофлот" <?= $f_airline == 'Аэрофлот' ? 'selected' : '' ?>>Аэрофлот</option>
                            <option value="S7 Airlines" <?= $f_airline == 'S7 Airlines' ? 'selected' : '' ?>>S7 Airlines</option>
                            <option value="Lufthansa" <?= $f_airline == 'Lufthansa' ? 'selected' : '' ?>>Lufthansa</option>
                            <option value="Emirates" <?= $f_airline == 'Emirates' ? 'selected' : '' ?>>Emirates</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label class="filter-label">Цена</label>
                        <select name="f_price" onchange="this.form.submit()">
                            <option value="all" <?= $f_price == 'all' ? 'selected' : '' ?>>Любая</option>
                            <option value="0-15000" <?= $f_price == '0-15000' ? 'selected' : '' ?>>До 15 000</option>
                            <option value="15000-30000" <?= $f_price == '15000-30000' ? 'selected' : '' ?>>15к - 30к</option>
                            <option value="30000-50000" <?= $f_price == '30000-50000' ? 'selected' : '' ?>>30к - 50к</option>
                            <option value="50000+" <?= $f_price == '50000+' ? 'selected' : '' ?>>Более 50к</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label class="filter-label">Показывать</label>
                        <select name="f_limit" onchange="this.form.submit()">
                            <option value="100" <?= $f_limit == '100' ? 'selected' : '' ?>>100</option>
                            <option value="250" <?= $f_limit == '250' ? 'selected' : '' ?>>250</option>
                            <option value="500" <?= $f_limit == '500' ? 'selected' : '' ?>>500</option>
                            <option value="1000" <?= $f_limit == '1000' ? 'selected' : '' ?>>1000</option>
                            <option value="all" <?= $f_limit == 'all' ? 'selected' : '' ?>>Все</option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Блок сортировки (4 пункта) -->
            <form method="GET" action="admin.php" class="filters-wrapper">
                <!-- Сохраняем все остальные параметры, чтобы они не сбросились при сортировке -->
                <?php if ($selected_user_id > 0): ?>
                    <input type="hidden" name="user_id" value="<?= $selected_user_id ?>">
                <?php endif; ?>
                <input type="hidden" name="f_from" value="<?= htmlspecialchars($f_from) ?>">
                <input type="hidden" name="f_to" value="<?= htmlspecialchars($f_to) ?>">
                <input type="hidden" name="f_airline" value="<?= htmlspecialchars($f_airline) ?>">
                <input type="hidden" name="f_price" value="<?= htmlspecialchars($f_price) ?>">
                <input type="hidden" name="f_limit" value="<?= htmlspecialchars($f_limit) ?>">

                <div class="sort-grid">
                    <div class="form-col">
                        <label class="filter-label">Сортировка по ID</label>
                        <select name="sort_id" onchange="clearOtherSorts('sort_id'); this.form.submit()">
                            <option value="id_desc" <?= $sort == 'id_desc' ? 'selected' : '' ?>>Сначала новые</option>
                            <option value="id_asc" <?= $sort == 'id_asc' ? 'selected' : '' ?>>Сначала старые</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label class="filter-label">Сортировка по цене</label>
                        <select name="sort_price" onchange="clearOtherSorts('sort_price'); this.form.submit()">
                            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>По возрастанию</option>
                            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>По убыванию</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label class="filter-label">Сортировка по длительности</label>
                        <select name="sort_dur" onchange="clearOtherSorts('sort_dur'); this.form.submit()">
                            <option value="duration_asc" <?= $sort == 'duration_asc' ? 'selected' : '' ?>>По возрастанию</option>
                            <option value="duration_desc" <?= $sort == 'duration_desc' ? 'selected' : '' ?>>По убыванию</option>
                        </select>
                    </div>
                    <div class="form-col">
                        <label class="filter-label">Сортировка по времени</label>
                        <select name="sort_time" onchange="clearOtherSorts('sort_time'); this.form.submit()">
                            <option value="time_asc" <?= $sort == 'time_asc' ? 'selected' : '' ?>>Сначала ранние</option>
                            <option value="time_desc" <?= $sort == 'time_desc' ? 'selected' : '' ?>>Сначала поздние</option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Таблица -->
            <div class="table-responsive table-scrollable">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>ID</th>
                            <th>Маршрут</th>
                            <th>Время</th>
                            <th>Длительность</th>
                            <th>Рейс</th>
                            <th>Цена</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($flights)): ?>
                            <tr><td colspan="8" class="text-center">Рейсы по вашему запросу не найдены</td></tr>
                        <?php endif; ?>
                        
                        <?php
                        $counter = 1;
                        foreach ($flights as $f):
                        ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= $f['id'] ?></td>
                            <td><?= htmlspecialchars($f['from_city']) ?> — <?= htmlspecialchars($f['to_city']) ?></td>
                            <td><?= $f['departure_time'] ?> - <?= $f['arrival_time'] ?></td>
                            <td><?= htmlspecialchars($f['duration']) ?> / <?= $f['duration_min'] ?>м</td>
                            <td><?= htmlspecialchars($f['flight_number']) ?></td>
                            <td><?= number_format($f['price'], 0, '', ' ') ?> ₽</td>
                            <td class="actions-cell">
                                <a href="admin.php?edit_id=<?= $f['id'] ?>" class="btn btn-sm">✎</a>
                                <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить рейс?');">
                                    <input type="hidden" name="action" value="delete_flight">
                                    <input type="hidden" name="flight_id" value="<?= $f['id'] ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm">✖</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Небольшой скрипт, чтобы при изменении одного из 4-х видов сортировки, остальные сбрасывались -->
    <script>
        function clearOtherSorts(changedName) {
            const selects = ['sort_id', 'sort_price', 'sort_dur', 'sort_time'];
            selects.forEach(name => {
                if (name !== changedName) {
                    document.getElementsByName(name)[0].value = '';
                }
            });
        }
    </script>
</body>
</html>