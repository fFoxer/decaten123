<?php
$conn = new mysqli('localhost', 'root', '', 'food_service');
if ($conn->connect_error) die("Ошибка: " . $conn->connect_error);
session_start();
?>
