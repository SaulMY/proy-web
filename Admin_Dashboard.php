<?php
session_start();

// Verificar si el usuario está logueado y si es admin (tipo 1)
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 1) {
    // Redirigir a la página de inicio si no es admin
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/Styles.css">
    <title>Panel de Administrador</title>
</head>
<body>
    <header>
        <h1>Panel de Administrador</h1>
        <nav>
            <a href="agregar.php">Agregar Ingresos/Gastos</a>
            <a href="balance.php">Ver Balance</a>
            <a href="graficos.php">Gráficos</a>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>
    <main>
        <h2>Bienvenido, Administrador!</h2>
        <p>Desde aquí puedes gestionar todas las opciones del sistema.</p>
    </main>
</body>
</html>
