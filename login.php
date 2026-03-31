<?php 
require 'db.php'; // Убедись, что в db.php есть проверка session_status()

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $p = $_POST['phone']; 
    $pass = $_POST['pass'];
    
    // --- ПРОВЕРКА НА АДМИНА ---
    if($p === 'admin' && $pass === 'admin') {
        $_SESSION['user_role'] = 'admin';
        $_SESSION['user_name'] = 'Администратор';
        header("Location: admin.php");
        exit;
    }

    if($_POST['act'] == 'reg') {
        $n = $_POST['name'];
        $hp = password_hash($pass, PASSWORD_DEFAULT);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | FoodFast</title>
    <!-- Подключаем шрифт Creepster для лого как в админке -->
    <link href="https://googleapis.com" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

    <div class="card" style="width: 360px; text-align: center;">
        <div class="logo" style="margin-bottom: 20px;">Food<span>Fast</span></div>
        <h2 id="t" style="margin-bottom: 20px; color: #fff;">Вход</h2>
        
        <form method="POST">
            <input type="hidden" name="act" id="act" value="login">
            
            <input type="text" name="name" id="in" class="auth-input" placeholder="Ваше имя" style="display:none;">
            <input type="text" name="phone" class="auth-input" placeholder="Логин (admin)" required>
            <input type="password" name="pass" class="auth-input" placeholder="Пароль (admin)" required>
            
            <button type="submit" class="btn btn-primary btn-full">Продолжить</button>
        </form>
        
        <button onclick="toggle(event)" class="btn btn-primary btn-full btn-switch" id="toggle-btn">Создать аккаунт</button>
    </div>

    <script>
        function toggle(e) {
            e.preventDefault();
            const isL = document.getElementById('act').value === 'login';
            document.getElementById('act').value = isL ? 'reg' : 'login';
            document.getElementById('in').style.display = isL ? 'block' : 'none';
            document.getElementById('in').required = isL;
            
            document.getElementById('t').innerText = isL ? 'Регистрация' : 'Вход';
            document.getElementById('toggle-btn').innerText = isL ? 'Уже есть аккаунт? Войти' : 'Создать аккаунт';
        }
    </script>
</body>
</html>
