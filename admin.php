<?php require 'db.php'; 

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); exit;
}

// --- 1. УДАЛЕНИЕ ---
if (isset($_GET['delete_res'])) {
    $id = intval($_GET['delete_res']);
    $conn->query("DELETE FROM restaurants WHERE id = $id");
    header("Location: admin.php"); exit;
}
if (isset($_GET['delete_food'])) {
    $id = intval($_GET['delete_food']);
    $conn->query("DELETE FROM food_items WHERE id = $id");
    header("Location: admin.php"); exit;
}

// --- 2. СОХРАНЕНИЕ (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Сохранение РЕСТОРАНА
    if ($_POST['type'] == 'res') {
        $n = $conn->real_escape_string($_POST['name']);
        $i = $conn->real_escape_string($_POST['img']);
        $tm = $conn->real_escape_string($_POST['time']);
        $id = intval($_POST['res_id']);
        // Склеиваем массив тегов в строку "burgers,snacks"
        $t = isset($_POST['tags']) ? implode(',', $_POST['tags']) : '';

        if ($id > 0) $conn->query("UPDATE restaurants SET name='$n', img='$i', tag='$t', delivery_time='$tm' WHERE id=$id");
        else $conn->query("INSERT INTO restaurants (name, img, tag, delivery_time) VALUES ('$n', '$i', '$t', '$tm')");
    } 
    // Сохранение БЛЮДА
    else if ($_POST['type'] == 'food') {
        $rid = intval($_POST['res_id']);
        $fn = $conn->real_escape_string($_POST['f_name']);
        $fp = intval($_POST['f_price']);
        $fi = $conn->real_escape_string($_POST['f_img']);
        $fid = intval($_POST['food_id']);

        if ($fid > 0) $conn->query("UPDATE food_items SET restaurant_id='$rid', name='$fn', price='$fp', img='$fi' WHERE id=$fid");
        else $conn->query("INSERT INTO food_items (restaurant_id, name, price, img) VALUES ('$rid', '$fn', '$fp', '$fi')");
    }
    header("Location: admin.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>🎃 Полная Админ-панель FoodFast</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        .admin-section { margin-bottom: 60px; }
        .admin-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px; margin-top: 20px; }
        .admin-card { background: #2A2A2A; padding: 25px; border-radius: 20px; border: 1px solid #444; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .admin-card h2 { font-family: 'Creepster', cursive; font-size: 32px; color: var(--primary); margin: 0 0 20px; }
        
        /* Сетка чекбоксов */
        .tags-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: #333; padding: 15px; border-radius: 12px; margin: 10px 0 20px; border: 1px solid #444; }
        .tags-grid label { font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; color: #ccc; }
        .tags-grid input { width: auto; margin: 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; color: #eee; font-size: 14px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #444; }
        th { color: var(--primary); font-family: 'Creepster', cursive; font-size: 18px; }
        
        .edit-mode { border: 2px solid var(--primary) !important; background: #332211 !important; }
        .btn-edit { color: #5DADE2; cursor: pointer; font-weight: bold; margin-right: 15px; }
        .btn-del { color: #ff4d4d; font-weight: bold; text-decoration: none; }
        hr { border: 0; border-top: 2px solid #444; margin: 50px 0; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container flex-sb">
            <div class="logo" onclick="location.href='index.php'">Food<span>Fast</span> Admin 🎃</div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <span class="user-greeting">💀 Привет, <b><?= htmlspecialchars($_SESSION['user_name']) ?></b></span>
                <a href="index.php" class="btn btn-secondary">На сайт</a>
                <a href="logout.php" class="btn btn-primary">Выйти</a>
            </div>
        </div>
    </header>

    <main class="container">
        
        <!-- === СЕКЦИЯ 1: РЕСТОРАНЫ === -->
        <section class="admin-section">
            <div class="admin-grid">
                <div class="admin-card" id="res-form-block">
                    <h2 id="res-title">👻 Рестораны</h2>
                    <form method="POST" id="res-form">
                        <input type="hidden" name="type" value="res">
                        <input type="hidden" name="res_id" id="res_id" value="0">
                        <input type="text" name="name" id="r_name" class="auth-input" placeholder="Название заведения" required>
                        <input type="text" name="img" id="r_img" class="auth-input" placeholder="URL фото">
                        
                        <label style="color:var(--primary); font-size:14px; font-weight:bold;">Категории (можно несколько):</label>
                        <div class="tags-grid">
                            <?php
                            $tags_list = [
                                'burgers'=>'🍔 Бургеры','pizza'=>'🍕 Пицца','sushi'=>'🕷️ Суши',
                                'khinkali'=>'🥟 Хинкали','shawarma'=>'🌯 Шаурма','wok'=>'🍜 WOK',
                                'snacks'=>'🍟 Снэки','drinks'=>'🥤 Напитки','desserts'=>'🍬 Десерты'
                            ];
                            foreach($tags_list as $val => $name): ?>
                                <label><input type="checkbox" name="tags[]" value="<?= $val ?>" class="tag-ch"> <?= $name ?></label>
                            <?php endforeach; ?>
                        </div>

                        <input type="text" name="time" id="r_time" class="auth-input" placeholder="Время доставки">
                        <button type="submit" id="res-btn" class="btn btn-primary btn-full">Сохранить заведение</button>
                        <button type="button" onclick="resetResForm()" id="res-cancel" class="btn btn-secondary btn-full" style="display:none; margin-top:10px;">Отмена</button>
                    </form>
                </div>
                <div class="admin-card">
                    <h2>📜 Список заведений</h2>
                    <table>
                        <thead><tr><th>Имя</th><th>Действие</th></tr></thead>
                        <tbody>
                            <?php $res = $conn->query("SELECT * FROM restaurants ORDER BY id DESC");
                            while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td>
                                        <a class="btn-edit" onclick='editRes(<?= json_encode($row) ?>)'>📝 Изменить </a>
                                        <a href="?delete_res=<?= $row['id'] ?>" class="btn-del">☠️ Удалить </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <hr>

        <!-- === СЕКЦИЯ 2: БЛЮДА === -->
        <section class="admin-section">
            <div class="admin-grid">
                <div class="admin-card" id="food-form-block">
                    <h2 id="food-title">💀 Меню блюд</h2>
                    <form method="POST" id="food-form">
                        <input type="hidden" name="type" value="food">
                        <input type="hidden" name="food_id" id="food_id" value="0">
                        
                        <select name="res_id" id="f_res_id" class="auth-input" required>
                            <option value="">Куда добавить блюдо?</option>
                            <?php $res_l = $conn->query("SELECT id, name FROM restaurants");
                            while($r = $res_l->fetch_assoc()) echo "<option value='{$r['id']}'>{$r['name']}</option>"; ?>
                        </select>
                        
                        <input type="text" name="f_name" id="f_name" class="auth-input" placeholder="Название блюда" required>
                        <input type="number" name="f_price" id="f_price" class="auth-input" placeholder="Цена (₽)" required>
                        <input type="text" name="f_img" id="f_img" class="auth-input" placeholder="URL фото блюда">
                        
                        <button type="submit" id="food-btn" class="btn btn-primary btn-full">Добавить в меню</button>
                        <button type="button" onclick="resetFoodForm()" id="food-cancel" class="btn btn-secondary btn-full" style="display:none; margin-top:10px;">Отмена</button>
                    </form>
                </div>
                <div class="admin-card">
                    <h2>🍔 Последние позиции</h2>
                    <table>
                        <thead><tr><th>Блюдо</th><th>Цена</th><th>Действие</th></tr></thead>
                        <tbody>
                            <?php $foods = $conn->query("SELECT f.*, r.name as rname FROM food_items f JOIN restaurants r ON f.restaurant_id = r.id ORDER BY f.id DESC LIMIT 10");
                            while($f = $foods->fetch_assoc()): ?>
                                <tr>
                                    <td><b><?= htmlspecialchars($f['name']) ?></b><br><small style="color:#777"><?= htmlspecialchars($f['rname']) ?></small></td>
                                    <td><?= $f['price'] ?> ₽</td>
                                    <td>
                                        <a class="btn-edit" onclick='editFood(<?= json_encode($f) ?>)'>📝 Изменить </a>
                                        <a href="?delete_food=<?= $f['id'] ?>" class="btn-del">❌ Удалить </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script>
        // ЛОГИКА РЕСТОРАНОВ
        function editRes(data) {
            document.getElementById('res-title').innerText = "🔮 Правка: " + data.name;
            document.getElementById('res_id').value = data.id;
            document.getElementById('r_name').value = data.name;
            document.getElementById('r_img').value = data.img;
            document.getElementById('r_time').value = data.delivery_time;
            
            // Сброс и простановка чекбоксов
            document.querySelectorAll('.tag-ch').forEach(ch => ch.checked = false);
            if(data.tag) {
                data.tag.split(',').forEach(t => {
                    const ch = document.querySelector(`.tag-ch[value="${t}"]`);
                    if(ch) ch.checked = true;
                });
            }

            document.getElementById('res-btn').innerText = "Сохранить правки";
            document.getElementById('res-cancel').style.display = "block";
            document.getElementById('res-form-block').classList.add('edit-mode');
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        function resetResForm() {
            document.getElementById('res-form').reset();
            document.getElementById('res_id').value = "0";
            document.getElementById('res-title').innerText = "👻 Рестораны";
            document.getElementById('res-btn').innerText = "Сохранить заведение";
            document.getElementById('res-cancel').style.display = "none";
            document.getElementById('res-form-block').classList.remove('edit-mode');
        }

        // ЛОГИКА БЛЮД
        function editFood(data) {
            document.getElementById('food-title').innerText = "🔮 Правка блюда";
            document.getElementById('food_id').value = data.id;
            document.getElementById('f_res_id').value = data.restaurant_id;
            document.getElementById('f_name').value = data.name;
            document.getElementById('f_price').value = data.price;
            document.getElementById('f_img').value = data.img;
            document.getElementById('food-btn').innerText = "Сохранить правки";
            document.getElementById('food-cancel').style.display = "block";
            document.getElementById('food-form-block').classList.add('edit-mode');
            document.getElementById('food-form-block').scrollIntoView({behavior: 'smooth'});
        }

        function resetFoodForm() {
            document.getElementById('food-form').reset();
            document.getElementById('food_id').value = "0";
            document.getElementById('food-title').innerText = "💀 Меню блюд";
            document.getElementById('food-btn').innerText = "Добавить в меню";
            document.getElementById('food-cancel').style.display = "none";
            document.getElementById('food-form-block').classList.remove('edit-mode');
        }
    </script>
</body>
</html>
