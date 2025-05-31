<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

header("Content-Type: application/json");
echo json_encode(['success' => true]);