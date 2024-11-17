<?php
$servername = "localhost";
$username = "root";  // O el nombre de usuario que uses
$password = "";      // O la contraseña correspondiente
$dbname = "BudGet";  // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
