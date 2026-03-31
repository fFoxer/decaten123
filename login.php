<?php 
session_start(); // ОБЯЗАТЕЛЬНО для работы авторизации
require 'db.php'; 

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $p = $_POST['phone']; 
    $pass = $_POST['pass'];
    
    // --- ПРОВЕРКА НА АДМИНА ---
    if($p === 'admin' && $pass === 'admin') {
        $_SESSION['user_role'] = 'admin';
        $_SESSION['user_name'] = 'Администратор';
        header("Location: admin.php"); // Уходим в админку
        exit;
    }
    // --------------------------

    if($_POST['act'] == 'reg') {
        $n = $_POST['name'];
        $hp = password_hash($pass, PASSWORD_DEFAULT);
        // Используй подготовленные выражения в будущем для защиты от инъекций!
        $conn->query("INSERT INTO users (name, phone, password) VALUES ('$n', '$p', '$hp')");
        $_SESSION['user_name'] = $n;
        $_SESSION['user_role'] = 'user';
    } else {
        $res = $conn->query("SELECT * FROM users WHERE phone='$p'");
        if ($res && $u = $res->fetch_assoc()) {
            if(password_verify($pass, $u['password'])) {
                $_SESSION['user_name'] = $u['name'];
                $_SESSION['user_role'] = 'user';
            }
        }
    }
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="display:flex; justify-content:center; align-items:center; min-height:100vh; background: #f4f4f4;">
    <div style="background:#fff; padding:40px; border-radius:24px; width:320px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 id="t">Вход</h2>
        <form method="POST">
            <input type="hidden" name="act" id="act" value="login">
            <input type="text" name="name" id="in" placeholder="Имя" style="display:none; width:100%; padding:10px; margin-bottom:10px; border: 1px solid #ddd; border-radius: 8px;">
            <!-- Для админа вводи сюда "admin" -->
            <input type="text" name="phone" placeholder="Телефон или логин" required style="width:100%; padding:10px; margin-bottom:10px; border: 1px solid #ddd; border-radius: 8px;">
            <input type="password" name="pass" placeholder="Пароль" required style="width:100%; padding:10px; margin-bottom:10px; border: 1px solid #ddd; border-radius: 8px;">
            <button type="submit" style="width:100%; padding:12px; background: orange; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Продолжить</button>
        </form>
        <button onclick="toggle()" style="background:none; border:none; color:orange; margin-top:20px; cursor:pointer; width: 100%;">Создать аккаунт</button>
    </div>

    <script>
        function toggle() {
            const isL = document.getElementById('act').value === 'login';
            document.getElementById('act').value = isL ? 'reg' : 'login';
            document.getElementById('in').style.display = isL ? 'block' : 'none';
            document.getElementById('t').innerText = isL ? 'Регистрация' : 'Вход';
            event.target.innerText = isL ? 'Уже есть аккаунт? Войти' : 'Создать аккаунт';
        }
    </script>
</body>
</html>
