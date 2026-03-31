<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статус заказа — FoodFast</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Creepster&display=swap" rel="stylesheet">
    <style>
        .map-container { 
            width: 100%; height: 450px; 
            background: url('tver-map.png') center no-repeat; 
            background-size: cover;
            position: relative; border-bottom: 2px solid #444;
            overflow: hidden;
            filter: grayscale(80%) brightness(50%); /* Делаем карту темной и серой */
        }
        .courier-path { position: absolute; width: 100%; height: 100%; }
        .bike { 
            position: absolute; font-size: 45px; 
            bottom: 50px; left: 50px; 
            transition: all 12s cubic-bezier(0.4, 0, 0.2, 1); 
            z-index: 10; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));
            transform: scaleX(-1); /* Поворачиваем иконку */
        }
        .home-point { 
            position: absolute; font-size: 55px; 
            top: 80px; right: 120px; 
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));
        }
        .order-panel { 
            max-width: 550px; margin: -50px auto 40px; position: relative; z-index: 100;
            background: #2A2A2A; color: #eee; padding: 35px; border-radius: 30px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.3); 
            border: 1px solid #444;
        }
        .status-badge { display: inline-flex; align-items: center; gap: 10px; background: #333; color: var(--primary); padding: 10px 20px; border-radius: 15px; font-weight: bold; margin-bottom: 20px; }
        .pulse { width: 12px; height: 12px; background: var(--primary); border-radius: 50%; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(255, 92, 0, 0.4); } 70% { box-shadow: 0 0 0 15px rgba(255, 92, 0, 0); } 100% { box-shadow: 0 0 0 0 rgba(255, 92, 0, 0); } }
        
        .item-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #444; }
        .finish-btn { display: none; width: 100%; margin-top: 25px; background: var(--primary); color: white; padding: 18px; border-radius: 15px; border: none; font-weight: bold; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body style="background: #1A1A1A;">
    <header class="header">
        <div class="container flex-sb">
            <div class="logo" onclick="location.href='index.php'" style="cursor:pointer">Food<span>Fast</span></div>
            <div style="font-weight: 600; color: #aaa;">Заказ №<?= rand(100, 999) ?></div>
        </div>
    </header>
    
    <div class="map-container">
        <div class="courier-path">
            <div class="home-point">🏚️</div>
            <div class="bike" id="bike">💀</div>
        </div>
    </div>
    <main class="container">
        <div class="order-panel">
            <div class="status-badge"><div class="pulse"></div> Курьер-скелет везёт ваш заказ</div>
            <h1 style="margin: 0 0 10px 0; font-family: 'Creepster', cursive; color: #fff;">Почти у вашего склепа!</h1>
            <p style="color: #bbb; font-size: 16px;">Доставим на: <b id="order-addr" style="color: #fff;">...</b></p>
            
            <div style="background: #1f1f1f; padding: 25px; border-radius: 20px; margin-top: 30px;">
                <h4 style="margin-top:0; color:#ddd;">Ваше зелье:</h4>
                <div id="items-list"></div>
                <div style="display:flex; justify-content:space-between; margin-top:15px; font-weight:bold; font-size:20px; border-top: 2px solid #444; padding-top: 15px;">
                    <span>Итого:</span><span id="order-total">0 ₽</span>
                </div>
            </div>
            <button id="finish-btn" class="finish-btn" onclick="location.href='index.php'">Заказ получен! Оставить отзыв</button>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const address = localStorage.getItem('last_address') || "Тверь, Центр";
            const cart = JSON.parse(localStorage.getItem('order_details')) || [];
            document.getElementById('order-addr').innerText = address;

            let total = 0;
            const listEl = document.getElementById('items-list');
            cart.forEach(i => {
                total += i.price * i.qty;
                listEl.innerHTML += `<div class="item-row"><span>${i.name} x${i.qty}</span><span>${i.price * i.qty} ₽</span></div>`;
            });
            document.getElementById('order-total').innerText = total + ' ₽';

            // Анимация курьера по картинке
            const bike = document.getElementById('bike');
            const btn = document.getElementById('finish-btn');

            setTimeout(() => {
                // Плавное движение к координатам дома на картинке
                bike.style.left = 'calc(100% - 180px)';
                bike.style.bottom = 'calc(100% - 140px)';
            }, 800);

            // Появление кнопки через 12 секунд
            setTimeout(() => {
                bike.innerHTML = '✅';
                btn.style.display = 'block';
            }, 12500);
        });
    </script>
</body>
</html>
