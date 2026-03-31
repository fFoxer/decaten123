<?php
require 'db.php';

// 1. Получаем ID ресторана из URL и защищаем от взлома
$restaurant = null; // Инициализируем переменную как null
if (isset($_GET['id'])) {
    $rid = intval($_GET['id']);
    // 2. Загружаем данные о ресторане
    $res_query = $conn->query("SELECT * FROM restaurants WHERE id = $rid");
    if ($res_query && $res_query->num_rows > 0) {
        $restaurant = $res_query->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <!-- Заголовок теперь тоже проверяет, найден ли ресторан -->
    <title><?= $restaurant ? htmlspecialchars($restaurant['name']) . ' — Жуткое Меню' : 'Ресторан не найден' ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet">
    <style>
        /* Стили остаются без изменений */
        .qty-controls { display: none; align-items: center; gap: 10px; background: #333; border-radius: 12px; padding: 5px; }
        .qty-controls.active { display: flex; }
        .btn-qty { width: 30px; height: 30px; border: none; background: #444; color: #eee; border-radius: 8px; cursor: pointer; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .hidden { display: none; }
        .header-banner { background: #222; padding: 40px 0; border-bottom: 1px solid #444; margin-bottom: 30px; }
        .header-banner h1 { font-family: 'Creepster', cursive; letter-spacing: 2px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
        .card { background: #2A2A2A; border-radius: 20px; overflow: hidden; transition: 0.3s; border: 1px solid #444; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(255, 92, 0, 0.3); }
        .error-container { text-align:center; padding:80px 20px; background:#2A2A2A; border-radius:30px; border: 1px solid #444; margin-top: 50px;}
    </style>
</head>
<body style="background: #1A1A1A; color: #eee;">
    <header class="header">
        <div class="container flex-sb">
            <div class="logo" onclick="location.href='index.php'">Food<span>Fast</span></div>
            <a href="cart.html" class="btn btn-primary" style="text-decoration:none; font-size: 20px;">🎃 <span id="cart-count">0</span></a>
        </div>
    </header>

    <?php if ($restaurant): // ГЛАВНАЯ ПРОВЕРКА: если ресторан найден, показываем страницу ?>
    
        <div class="header-banner">
            <div class="container">
                <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: bold;">← Все рестораны</a>
                <h1 style="margin-top: 20px; font-size: 52px; color: #fff;"><?= htmlspecialchars($restaurant['name']) ?></h1>
                <p style="color: #aaa; font-size: 18px;">
                    <?= htmlspecialchars($restaurant['tag']) ?> • Доставка <?= htmlspecialchars($restaurant['delivery_time']) ?>
                </p>
            </div>
        </div>
        <main class="container">
            <div class="grid">
                <?php
                // 3. Загружаем блюда ИМЕННО ЭТОГО ресторана
                // Переменная $rid уже определена выше
                $food_res = $conn->query("SELECT * FROM food_items WHERE restaurant_id = $rid");
                
                if ($food_res->num_rows > 0):
                    while($item = $food_res->fetch_assoc()): 
                ?>
                    <article class="card" id="food-<?= $item['id'] ?>">
                        <div class="card-img" style="height:200px; background: url('<?= htmlspecialchars($item['img']) ?>') center/cover;"></div>
                        <div style="padding: 20px;">
                            <h3 class="p-name"><?= htmlspecialchars($item['name']) ?></h3>
                            <div class="flex-sb">
                                <span style="font-weight: bold;"><span class="p-price"><?= $item['price'] ?></span> ₽</span>
                                <button class="btn btn-primary b-add" onclick="changeQty('food-<?= $item['id'] ?>', 1)">+</button>
                                <div class="qty-controls">
                                    <button class="btn-qty" onclick="changeQty('food-<?= $item['id'] ?>', -1)">-</button>
                                    <span class="iq">0</span>
                                    <button class="btn-qty" onclick="changeQty('food-<?= $item['id'] ?>', 1)">+</button>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php 
                    endwhile; 
                else:
                    echo "<h3>В этом ресторане пока нет блюд.</h3>";
                endif;
                ?>
            </div>
        </main>

    <?php else: // А если ресторан НЕ найден, выводим красивое сообщение об ошибке ?>
    
        <main class="container">
            <div class="error-container">
                <span style="font-size: 50px;">🤷‍♂️</span>
                <h2 style="font-family: 'Creepster', cursive; font-size: 44px; color: var(--primary);">Ресторан не найден</h2>
                <p style="color:#aaa;">Возможно, он был удален или вы перешли по неверной ссылке.</p>
                <br>
                <a href="index.php" class="btn btn-primary">Вернуться на главную</a>
            </div>
        </main>

    <?php endif; // Завершаем проверку ?>

    <script>
        function changeQty(id, d) {
            const c = document.getElementById(id);
            const q = c.querySelector('.iq');
            const b = c.querySelector('.b-add');
            const ctr = c.querySelector('.qty-controls');
            let n = parseInt(q.innerText) + d;
            n = n < 0 ? 0 : n;
            q.innerText = n;
            b.classList.toggle('hidden', n > 0);
            ctr.classList.toggle('active', n > 0);
            saveCart();
        }

        function saveCart() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            document.querySelectorAll('.card').forEach(c => {
                const qty = parseInt(c.querySelector('.iq').innerText);
                const itemId = c.id;
                cart = cart.filter(i => i.id !== itemId);
                if(qty > 0) {
                    cart.push({
                        id: itemId,
                        name: c.querySelector('.p-name').innerText,
                        price: parseInt(c.querySelector('.p-price').innerText),
                        qty: qty,
                        img: c.querySelector('.card-img').style.backgroundImage.slice(5, -2).replace(/"/g, "")
                    });
                }
            });
            localStorage.setItem('cart', JSON.stringify(cart));
            updateHeader();
        }

        function updateHeader() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            document.getElementById('cart-count').innerText = cart.reduce((acc, i) => acc + i.qty, 0);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.forEach(item => {
                const c = document.getElementById(item.id);
                if(c) {
                    c.querySelector('.iq').innerText = item.qty;
                    c.querySelector('.b-add').classList.add('hidden');
                    c.querySelector('.qty-controls').classList.add('active');
                }
            });
            updateHeader();
        });
    </script>


</body>
</html>

