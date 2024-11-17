<?php
session_start();

$host = 'localhost'; // Cambia esto si tu base de datos está en otro servidor
$dbname = 'BudGet';
$username = 'root'; // Tu usuario de MySQL
$password = ''; // Tu contraseña de MySQL

try {
    // Crear conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Recibir datos del formulario
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];

        // Consulta SQL para obtener el usuario
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        // Verificar si el usuario existe
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['password'] == $password) {
            // Usuario autenticado, guardar en sesión
            $_SESSION['usuario'] = $user['username'];
            $_SESSION['tipo'] = $user['type']; // Guardamos el tipo de usuario (0 para usuario normal, 1 para admin)

            // Redirigir según el tipo de usuario
            if ($user['type'] == 1) {
                // Redirigir al área de admin si es admin
                header("Location: admin_dashboard.php"); // Redirigir a página de admin (puedes crear una página específica para admin)
            } else {
                // Redirigir a página principal del usuario normal
                header("Location: index.php"); // Redirigir al índice del usuario normal
            }
            exit();
        } else {
            // Error: credenciales incorrectas
            header("Location: index.php?error=1"); // Volver al login con error
            exit();
        }
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
