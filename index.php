<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodFast — Доставка из ресторанов</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Стили для сетки ресторанов */
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
            gap: 30px; 
            margin-top: 20px; 
        }
        
        .restaurant-card {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: block;
            border: 1px solid #eee;
        }

        .restaurant-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        .res-img {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .res-badge {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: #fff;
            padding: 5px 12px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 14px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .res-info { padding: 20px; }
        .res-info h3 { margin: 0 0 8px 0; font-size: 22px; }
        .res-meta { color: #888; font-size: 15px; display: flex; gap: 10px; }
        
        .categories { display: flex; gap: 12px; margin: 30px 0; overflow-x: auto; padding-bottom: 5px; }
        .cat-item { 
            background: #fff; padding: 12px 24px; border-radius: 25px; 
            cursor: pointer; white-space: nowrap; transition: 0.3s;
            border: 1px solid #eee; font-weight: 500;
        }
        .cat-item.active { background: var(--primary); color: #fff; border-color: var(--primary); }
        .restaurant-card.hidden { display: none; }
    </style>
</head>
<body>

    <header class="header">
        <div class="container flex-sb">
            <div class="logo" onclick="location.href='index.php'">Food<span>Fast</span></div>
            
            <div class="auth-block" style="display: flex; align-items: center; gap: 20px;">
                <?php if(isset($_SESSION['user_name'])): ?>
                    <span style="font-size: 14px;">Привет, <b><?= htmlspecialchars($_SESSION['user_name']) ?></b></span>
                    <a href="logout.php" style="font-size: 12px; color: #888; text-decoration: none;">Выйти</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">Войти</a>
                <?php endif; ?>
                
                <a href="cart.html" class="btn btn-primary" style="text-decoration: none;">
                    🛒 <span id="cart-count">0</span>
                </a>
            </div>
        </div>
    </header>

    <main class="container">
        <h1 style="margin-top: 40px; font-size: 36px;">Рестораны в Твери</h1>

        <!-- Фильтр по типам кухни -->
        <section class="categories">
            <div class="cat-item active" data-filter="all">Все</div>
            <div class="cat-item" data-filter="burgers">🍔 Бургеры</div>
            <div class="cat-item" data-filter="pizza">🍕 Пицца</div>
            <div class="cat-item" data-filter="sushi">🍣 Суши</div>
            <div class="cat-item" data-filter="desserts">🍰 Десерты</div>
        </section>

        <div class="grid">
            <?php
            // Получаем список ресторанов из базы данных
            $query = "SELECT * FROM restaurants";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <a href="restaurant.php?id=<?= $row['id'] ?>" class="restaurant-card" data-cat="<?= $row['tag'] ?>">
                        <div class="res-img" style="background-image: url('<?= $row['img'] ?>')">
                            <div class="res-badge"><?= $row['delivery_time'] ?></div>
                        </div>
                        <div class="res-info">
                            <h3><?= htmlspecialchars($row['name']) ?></h3>
                            <div class="res-meta">
                                <span>★ 4.8</span>
                                <span>•</span>
                                <span><?= htmlspecialchars($row['tag']) ?></span>
                            </div>
                        </div>
                    </a>
                    <?php
                }
            } else {
                echo "<p>Рестораны пока не добавлены в базу данных.</p>";
            }
            ?>
        </div>
    </main>

    <script>
        // 1. Фильтрация ресторанов по категориям
        document.querySelectorAll('.cat-item').forEach(item => {
            item.onclick = () => {
                document.querySelector('.cat-item.active').classList.remove('active');
                item.classList.add('active');
                
                const filter = item.dataset.filter;
                document.querySelectorAll('.restaurant-card').forEach(card => {
                    if (filter === 'all' || card.dataset.cat === filter) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            }
        });

        // 2. Обновление счетчика корзины (общий для всех страниц)
        function updateGlobalBadge() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const count = cart.reduce((sum, item) => sum + item.qty, 0);
            document.getElementById('cart-count').innerText = count;
        }

        document.addEventListener('DOMContentLoaded', updateGlobalBadge);
    </script>

</body>
</html>
