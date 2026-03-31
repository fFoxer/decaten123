<?php
$conn = new mysqli('localhost', 'root', '', 'food_service');
if ($conn->connect_error) die("Ошибка: " . $conn->connect_error);

// Умный запуск сессии: проверяет, не запущена ли она уже
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
