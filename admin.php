<?php require 'db.php'; 

// ОБРАБОТКА УДАЛЕНИЯ
if (isset($_GET['delete_res'])) {
    $id = intval($_GET['delete_res']);
    $conn->query("DELETE FROM restaurants WHERE id = $id");
    header("Location: admin.php"); // Перезагрузка, чтобы данные обновились
}

if (isset($_GET['delete_food'])) {
    $id = intval($_GET['delete_food']);
    $conn->query("DELETE FROM food_items WHERE id = $id");
    header("Location: admin.php");
}

// ОБРАБОТКА ДОБАВЛЕНИЯ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['type'] == 'res') {
        $n = $conn->real_escape_string($_POST['name']);
        $i = $conn->real_escape_string($_POST['img']);
        $t = $_POST['tag'];
        $tm = $conn->real_escape_string($_POST['time']);
        $conn->query("INSERT INTO restaurants (name, img, tag, delivery_time) VALUES ('$n', '$i', '$t', '$tm')");
    } else {
        $rid = intval($_POST['res_id']);
        $fn = $conn->real_escape_string($_POST['f_name']);
        $fp = intval($_POST['f_price']);
        $fi = $conn->real_escape_string($_POST['f_img']);
        $conn->query("INSERT INTO food_items (restaurant_id, name, price, img) VALUES ('$rid', '$fn', '$fp', '$fi')");
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>🎃 Админ-панель</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet">
    <style>
        .admin-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 30px; }
        .admin-card { background: #2A2A2A; padding: 25px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .admin-card h2 { font-family: 'Creepster', cursive; font-size: 36px; color: var(--primary); letter-spacing: 1px; }

        input, select { 
            width: 100%; 
            padding: 12px; 
            margin: 8px 0 15px; 
            border: 1px solid #555; 
            border-radius: 10px; 
            box-sizing: border-box;
            background: #333;
            color: #eee;
        }
        input:focus, select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 10px rgba(255, 92, 0, 0.5);
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #444; }
        th { color: var(--primary); }
        .btn-del { color: #ff6b6b; text-decoration: none; font-weight: bold; }
        .btn-del:hover { text-decoration: underline; color: #ff4d4d; }
    </style>
</head>
<body style="background: #1A1A1A; color: #eee;">
    <header class="header">
        <div class="container flex-sb">
            <div class="logo" onclick="location.href='index.php'">Food<span>Fast</span> Admin 🎃</div>
            <a href="index.php" class="btn btn-secondary">На сайт</a>
        </div>
    </header>
    <main class="container">
        <div class="admin-grid">
            
            <!-- УПРАВЛЕНИЕ РЕСТОРАНАМИ -->
            <div class="admin-card">
                <h2>👻 Рестораны</h2>
                <form method="POST">
                    <input type="hidden" name="type" value="res">
                    <input type="text" name="name" placeholder="Название" required>
                    <input type="text" name="img" placeholder="URL фото" required>
                    <select name="tag">
                        <option value="burgers">Бургеры</option>
                        <option value="pizza">Пицца</option>
                        <option value="sushi">Суши</option>
                        <option value="desserts">Десерты</option>
                    </select>
                    <input type="text" name="time" placeholder="Время (напр. 20 мин)">
                    <button type="submit" class="btn btn-primary btn-full">Добавить</button>
                </form>
                <table>
                    <tr><th>Название</th><th>Действие</th></tr>
                    <?php
                    $res = $conn->query("SELECT * FROM restaurants");
                    while($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><a href="?delete_res=<?= $row['id'] ?>" class="btn-del" onclick="return confirm('Удалить ресторан и всё его меню?')">Удалить ☠️</a></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <!-- УПРАВЛЕНИЕ БЛЮДАМИ -->
            <div class="admin-card">
                <h2>💀 Блюда</h2>
                <form method="POST">
                    <input type="hidden" name="type" value="food">
                    <select name="res_id" required>
                        <option value="">Куда добавить?</option>
                        <?php
                        $res_list = $conn->query("SELECT id, name FROM restaurants");
                        while($row = $res_list->fetch_assoc()) echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        ?>
                    </select>
                    <input type="text" name="f_name" placeholder="Название блюда" required>
                    <input type="number" name="f_price" placeholder="Цена">
                    <input type="text" name="f_img" placeholder="URL фото блюда">
                    <button type="submit" class="btn btn-primary btn-full">Добавить</button>
                </form>
                <table>
                    <tr><th>Блюдо</th><th>Ресторан</th><th>Удалить</th></tr>
                    <?php
                    $food = $conn->query("SELECT f.id, f.name, r.name as rname FROM food_items f JOIN restaurants r ON f.restaurant_id = r.id");
                    while($row = $food->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['rname']) ?></td>
                            <td><a href="?delete_food=<?= $row['id'] ?>" class="btn-del" onclick="return confirm('Удалить это блюдо?')">❌</a></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
