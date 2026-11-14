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
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h1 class="page-title">Панель администратора</h1>

        <!-- Выбор пользователя для просмотра броней -->
        <div class="card">
            <h2 class="section-title">Управление бронированиями</h2>
            <form method="GET" action="admin.php" class="filter-group" style="display: flex; gap: 15px; align-items: flex-end;">
                <div style="flex: 1;">
                    <label style="display:block; margin-bottom:8px; font-weight:bold;">Выберите пользователя:</label>
                    <select name="user_id" class="admin-select" style="width:100%; padding:10px;" required>
                        <option value="0">-- Выберите пользователя --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $selected_user_id == $u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['surname'] . ' ' . $u['name'] . ' (@' . $u['login'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Показать брони</button>
            </form>
        </div>

        <!-- Блок активных бронирований выбранного пользователя -->
        <?php if ($selected_user_id > 0): ?>
            <h3 class="section-title">Активные бронирования пользователя</h3>
            <?php if (empty($active_bookings)): ?>
                <div class="card"><p>У этого пользователя нет активных бронирований.</p></div>
            <?php else: ?>
                <div class="card">
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Код</th>
                                    <th>Маршрут</th>
                                    <th>Дата вылета</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($active_bookings as $b): 
                                    $statusClass = 'select-' . $b['status'];
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($b['code']) ?></strong></td>
                                    <td><?= htmlspecialchars($b['from_city']) ?> — <?= htmlspecialchars($b['to_city']) ?></td>
                                    <td><?= date('d.m.Y', strtotime($b['departure_date'])) ?> в <?= $b['departure_time'] ?></td>
                                    <td>
                                        <form action="php/admin_action.php" method="POST" style="display:flex; gap:5px;">
                                            <input type="hidden" name="action" value="change_status">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <input type="hidden" name="current_user_id" value="<?= $selected_user_id ?>">
                                            <select name="status" class="admin-select <?= $statusClass ?>" onchange="this.form.submit()">
                                                <option value="active" <?= $b['status'] == 'active' ? 'selected' : '' ?>>Активно</option>
                                                <option value="confirmed" <?= $b['status'] == 'confirmed' ? 'selected' : '' ?>>Подтверждено</option>
                                                <option value="cancelled" <?= $b['status'] == 'cancelled' ? 'selected' : '' ?>>Отменено</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить бронирование?');">
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
                </div>
            <?php endif; ?>

            <!-- Блок истории бронирований -->
            <h3 class="section-title">История бронирований</h3>
            <?php if (empty($past_bookings)): ?>
                <div class="card"><p>История бронирований пуста.</p></div>
            <?php else: ?>
                <div class="card">
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Код</th>
                                    <th>Маршрут</th>
                                    <th>Дата вылета</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($past_bookings as $b): 
                                    $statusClass = 'select-' . $b['status'];
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($b['code']) ?></strong></td>
                                    <td><?= htmlspecialchars($b['from_city']) ?> — <?= htmlspecialchars($b['to_city']) ?></td>
                                    <td><?= date('d.m.Y', strtotime($b['departure_date'])) ?> в <?= $b['departure_time'] ?></td>
                                    <td>
                                        <form action="php/admin_action.php" method="POST" style="display:flex; gap:5px;">
                                            <input type="hidden" name="action" value="change_status">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <input type="hidden" name="current_user_id" value="<?= $selected_user_id ?>">
                                            <select name="status" class="admin-select <?= $statusClass ?>" onchange="this.form.submit()">
                                                <option value="active" <?= $b['status'] == 'active' ? 'selected' : '' ?>>Активно</option>
                                                <option value="confirmed" <?= $b['status'] == 'confirmed' ? 'selected' : '' ?>>Подтверждено</option>
                                                <option value="cancelled" <?= $b['status'] == 'cancelled' ? 'selected' : '' ?>>Отменено</option>
                                                <option value="completed" <?= $b['status'] == 'completed' ? 'selected' : '' ?>>Завершено</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить бронирование?');">
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
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Блок пользователей -->
        <div class="card">
            <h2 class="section-title">Зарегистрированные пользователи</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ФИО / Логин</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th>Роль</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['surname'] . ' ' . $u['name']) ?><br><small style="color:#666;">@<?= htmlspecialchars($u['login']) ?></small></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['phone']) ?></td>
                            <td>
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span style="color: #ff4081; font-weight: bold;">Админ</span>
                                <?php else: ?>
                                    Пользователь
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить пользователя и ВСЕ его бронирования?');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm" style="background: #333;">Удалить</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Блок сообщений -->
        <div class="card">
            <h2 class="section-title">Сообщения из формы обратной связи</h2>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Отправитель</th>
                            <th>Тема</th>
                            <th>Сообщение</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr><td colspan="5" style="text-align:center;">Нет сообщений</td></tr>
                        <?php endif; ?>
                        <?php foreach ($messages as $m): ?>
                        <tr>
                            <td><?= date('d.m.Y H:i', strtotime($m['created_at'])) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($m['name']) ?></strong><br>
                                <small><?= htmlspecialchars($m['email']) ?><br><?= htmlspecialchars($m['phone'] ?? '') ?></small>
                            </td>
                            <td><?= htmlspecialchars($m['subject']) ?></td>
                            <td class="message-cell"><?= nl2br(htmlspecialchars($m['message'])) ?></td>
                            <td>
                                <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить сообщение?');">
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
                    <button type="submit" class="btn"><?= $edit_flight ? 'Обновить рейс' : 'Добавить рейс' ?></button>
                    <?php if ($edit_flight): ?><a href="admin.php" class="btn btn-secondary" style="margin-left: 10px;">Отмена</a><?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Таблица рейсов -->
        <div class="card">
            <h2 class="section-title">Последние добавленные рейсы</h2>
            <div class="table-responsive table-scrollable">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Маршрут</th>
                            <th>Время</th>
                            <th>Рейс</th>
                            <th>Цена</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flights as $f): ?>
                        <tr>
                            <td><?= $f['id'] ?></td>
                            <td><?= htmlspecialchars($f['from_city']) ?> — <?= htmlspecialchars($f['to_city']) ?></td>
                            <td><?= $f['departure_time'] ?> - <?= $f['arrival_time'] ?></td>
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
</body>
</html>