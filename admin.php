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
                <li><a href="index.php">Главная</a></li>
                <li><a href="flights.php">Рейсы</a></li>
                <li><a href="admin.php" class="active" style="color: #ff4081; font-weight: bold;">Админ-панель</a></li>
                <li><a href="php/logout.php">Выход (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1 class="page-title">Панель администратора</h1>

        <!-- Блок бронирований -->
        <div class="card">
            <h2 class="section-title">Все бронирования</h2>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Код</th>
                            <th>Пользователь</th>
                            <th>Маршрут</th>
                            <th>Дата вылета</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($b['code']) ?></strong></td>
                            <td><?= htmlspecialchars($b['user_surname'] . ' ' . $b['user_name']) ?><br><small style="color:#666;">@<?= htmlspecialchars($b['user_login']) ?></small></td>
                            <td><?= htmlspecialchars($b['from_city']) ?> — <?= htmlspecialchars($b['to_city']) ?></td>
                            <td><?= date('d.m.Y', strtotime($b['departure_date'])) ?></td>
                            <td>
                                <!-- Форма для смены статуса -->
                                <form action="php/admin_action.php" method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="action" value="change_status">
                                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                    <select name="status" class="admin-select" onchange="this.form.submit()">
                                        <option value="active" <?= $b['status'] == 'active' ? 'selected' : '' ?>>Активно</option>
                                        <option value="confirmed" <?= $b['status'] == 'confirmed' ? 'selected' : '' ?>>Подтверждено</option>
                                        <option value="cancelled" <?= $b['status'] == 'cancelled' ? 'selected' : '' ?>>Отменено</option>
                                        <option value="completed" <?= $b['status'] == 'completed' ? 'selected' : '' ?>>Завершено</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <!-- Форма для удаления брони -->
                                <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить бронирование?');">
                                    <input type="hidden" name="action" value="delete_booking">
                                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                    <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 14px;">Удалить</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Блок пользователей -->
        <div class="card">
            <h2 class="section-title">Зарегистрированные пользователи</h2>
            <div style="overflow-x: auto;">
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
                                <?php if ($u['id'] != $_SESSION['user_id']): // Запрещаем удалять самого себя ?>
                                <form action="php/admin_action.php" method="POST" onsubmit="return confirm('Удалить пользователя и ВСЕ его бронирования?');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 14px; background: #333;">Удалить</button>
                                </form>
                                <?php endif; ?>
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