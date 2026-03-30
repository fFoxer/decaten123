<?php 
require 'db.php'; 

// 1. Получаем ID ресторана из URL и защищаем от взлома
if (!isset($_GET['id'])) { 
    header("Location: index.php"); 
    exit; 
}
$rid = intval($_GET['id']);

// 2. Загружаем данные о ресторане
$res_query = $conn->query("SELECT * FROM restaurants WHERE id = $rid");
$restaurant = $res_query->fetch_assoc();

// Если ресторан не найден в базе
if (!$restaurant) { 
    die("<div class='container'><h1>Ресторан не найден</h1><a href='index.php'>Вернуться на главную</a></div>"); 
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($restaurant['name']) ?> — Меню</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .qty-controls { display: none; align-items: center; gap: 10px; background: #f5f5f7; border-radius: 12px; padding: 5px; }
        .qty-controls.active { display: flex; }
        .btn-qty { width: 30px; height: 30px; border: none; background: #fff; border-radius: 8px; cursor: pointer; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .hidden { display: none; }
        .header-banner { background: #fff; padding: 40px 0; border-bottom: 1px solid #eee; margin-bottom: 30px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
    </style>
</head>
<body>

    <header class="header">
        <div class="container flex-sb">
            <div class="logo" onclick="location.href='index.php'">Food<span>Fast</span></div>
            <a href="cart.html" class="btn btn-primary" style="text-decoration:none;">🛒 <span id="cart-count">0</span></a>
        </div>
    </header>

    <div class="header-banner">
        <div class="container">
            <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: bold;">← Все рестораны</a>
            <h1 style="margin-top: 20px; font-size: 42px;"><?= htmlspecialchars($restaurant['name']) ?></h1>
            <p style="color: #888; font-size: 18px;">
                <?= htmlspecialchars($restaurant['tag']) ?> • Доставка <?= htmlspecialchars($restaurant['delivery_time']) ?>
            </p>
        </div>
    </div>

    <main class="container">
        <div class="grid">
            <?php
            // 3. Загружаем блюда ИМЕННО ЭТОГО ресторана
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
