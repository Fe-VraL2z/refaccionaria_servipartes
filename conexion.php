<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "refaccionaria_servipartes";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");