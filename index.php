<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodFast — Жуткая доставка!</title>
    <link rel="stylesheet" href="style.css">
    <!-- Подключаем жуткий шрифт -->
    <link href="https://googleapis.com" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container flex-sb">
            <div class="logo" onclick="location.href='index.php'">Food<span>Fast</span> 🎃</div>
            
            <div class="auth-block" style="display: flex; align-items: center; gap: 15px;">
                <?php if(isset($_SESSION['user_name'])): ?>
                    <span class="user-greeting">
                        💀 Привет, <b><?= htmlspecialchars($_SESSION['user_name']) ?></b>
                    </span>
                    
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="admin.php" class="btn btn-secondary">Админка</a>
                    <?php endif; ?>
                    
                    <a href="logout.php" class="btn btn-primary">Выйти</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Войти</a>
                <?php endif; ?>
                
                <a href="cart.html" class="btn btn-secondary" style="font-size: 20px; padding: 10px 15px;">
                    🎃 <span id="cart-count">0</span>
                </a>
            </div>
        </div>
    </header>

    <main class="container">
        <h1 style="margin-top: 40px; font-size: 52px; font-family: 'Creepster', cursive; text-align: center; color: #fff;">🎃 Жутко вкусные рестораны 🎃</h1>
        
        <!-- КАТЕГОРИИ СО СТРЕЛКАМИ (ТВОЙ ВАРИАНТ) -->
        <div class="categories-wrapper">
            <button class="nav-btn prev" onclick="scrollCats(-250)"> < </button>
            
            <section class="categories" id="cat-list">
                <div class="cat-item active" data-filter="all">Все</div>
                <div class="cat-item" data-filter="burgers">🍔 Бургеры</div>
                <div class="cat-item" data-filter="pizza">🍕 Пицца</div>
                <div class="cat-item" data-filter="sushi">🍣 Суши</div>
                <div class="cat-item" data-filter="pasta">🍝 Паста</div>
                <div class="cat-item" data-filter="russian">🥞 Русская</div>
                <div class="cat-item" data-filter="salads">🥗 Салаты</div>
                <div class="cat-item" data-filter="khinkali">🥟 Хинкали</div>
                <div class="cat-item" data-filter="shawarma">🌯 Шаурма</div>
                <div class="cat-item" data-filter="wok">🍜 WOK</div>
                <div class="cat-item" data-filter="snacks">🍟 Снэки</div>
                <div class="cat-item" data-filter="drinks">🥤 Напитки</div>
                <div class="cat-item" data-filter="desserts">🍩 Десерты</div>
            </section>

            <button class="nav-btn next" onclick="scrollCats(250)"> > </button>
        </div>

        <div class="grid">
            <?php
            $res = $conn->query("SELECT * FROM restaurants ORDER BY id DESC");
            if ($res && $res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    ?>
                    <!-- data-cat хранит теги для фильтрации, но на экран они не выводятся -->
                    <a href="restaurant.php?id=<?= $row['id'] ?>" class="restaurant-card" data-cat="<?= $row['tag'] ?>">
                        <div class="res-img" style="background-image: url('<?= $row['img'] ?>')">
                            <div class="res-badge"><?= htmlspecialchars($row['delivery_time']) ?></div>
                        </div>
                        <div class="res-info">
                            <h3><?= htmlspecialchars($row['name']) ?></h3>
                            <div style="color: #aaa; font-size: 15px;">
                                <span style="color: var(--primary);">★ 4.9</span> • Доставка FoodFast 🦇
                            </div>
                        </div>
                    </a>
                    <?php
                }
            } else {
                echo "<p style='color: #888; text-align: center; width: 100%; grid-column: 1/-1;'>Ресторанов пока нет.</p>";
            }
            ?>
        </div>
    </main>

    <script>
        // Функция для прокрутки категорий
        function scrollCats(distance) {
            const container = document.getElementById('cat-list');
            container.scrollBy({ left: distance, behavior: 'smooth' });
        }

        // Фильтрация ресторанов по нескольким категориям
        document.querySelectorAll('.cat-item').forEach(item => {
            item.onclick = () => {
                document.querySelector('.cat-item.active').classList.remove('active');
                item.classList.add('active');
                
                const filter = item.dataset.filter;
                document.querySelectorAll('.restaurant-card').forEach(card => {
                    // Разрезаем строку категорий из БД (например, "burgers,snacks") в массив
                    const restaurantTags = card.dataset.cat.split(',');
                    
                    if (filter === 'all' || restaurantTags.includes(filter)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            }
        });

        // Счетчик корзины
        function updateGlobalBadge() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const count = cart.reduce((sum, item) => sum + item.qty, 0);
            const el = document.getElementById('cart-count');
            if(el) el.innerText = count;
        }
        document.addEventListener('DOMContentLoaded', updateGlobalBadge);
    </script>
</body>
</html>
