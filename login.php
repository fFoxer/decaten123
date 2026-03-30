<?php require 'db.php'; 
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $p = $_POST['phone']; $pass = $_POST['pass'];
    if($_POST['act'] == 'reg') {
        $n = $_POST['name'];
        $hp = password_hash($pass, PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (name, phone, password) VALUES ('$n', '$p', '$hp')");
        $_SESSION['user_name'] = $n;
    } else {
        $res = $conn->query("SELECT * FROM users WHERE phone='$p'");
        $u = $res->fetch_assoc();
        if($u && password_verify($pass, $u['password'])) $_SESSION['user_name'] = $u['name'];
    }
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="display:flex; justify-content:center; align-items:center; min-height:100vh;">
    <div style="background:#fff; padding:40px; border-radius:24px; width:320px;">
        <h2 id="t">Вход</h2>
        <form method="POST">
            <input type="hidden" name="act" id="act" value="login">
            <input type="text" name="name" id="in" placeholder="Имя" style="display:none; width:100%; padding:10px; margin-bottom:10px;">
            <input type="tel" name="phone" placeholder="Телефон" required style="width:100%; padding:10px; margin-bottom:10px;">
            <input type="password" name="pass" placeholder="Пароль" required style="width:100%; padding:10px; margin-bottom:10px;">
            <button type="submit" class="btn btn-primary btn-full">Продолжить</button>
        </form>
        <button onclick="toggle()" style="background:none; border:none; color:orange; margin-top:20px; cursor:pointer;">Регистрация</button>
    </div>
    <script>
        function toggle() {
            const isL = document.getElementById('act').value === 'login';
            document.getElementById('act').value = isL ? 'reg' : 'login';
            document.getElementById('in').style.display = isL ? 'block' : 'none';
            document.getElementById('t').innerText = isL ? 'Регистрация' : 'Вход';
        }
    </script>
</body>
</html>
