<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, redirigir al login
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    <link rel="stylesheet" href="CSS/Styles.css">  <!-- Enlace al archivo CSS -->
</head>
<body>
    <header>
        <h1>Bienvenido a tu presupuesto personal</h1>
        <nav>
            <a href="agregar.php">Agregar Ingresos/Gastos</a>
            <a href="balance.php">Ver Balance</a>
            <a href="graficos.php">Gráficos</a>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>
    <main>
        <h2>Hola, <?php echo $_SESSION['usuario']; ?>!</h2>
        <p>Usa el menú para gestionar tus finanzas personales.</p>
    </main>
</body>
</html>
