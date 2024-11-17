<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/Styles.css">
    <title>Presupuesto Personal</title>
</head>

<body>
    <header>
        <h1>Aplicación de Presupuesto Personal</h1>
        <br>
        <nav>
            <?php
            // Verificar si el usuario está logueado
            if (isset($_SESSION['usuario'])) {
                echo '<a href="agregar.php">Agregar Ingresos/Gastos</a>';
                echo '<a href="balance.php">Ver Balance</a>';
                echo '<a href="graficos.php">Gráficos</a>';
                if ($_SESSION['tipo'] == 1) {
                    // Mostrar enlace de admin solo si es admin
                    echo '<a href="admin_dashboard.php">Panel de Admin</a>';
                }
                echo '<a href="logout.php">Cerrar Sesión</a>';
            } else {
                echo '<a href="index.php">Inicio</a>';
            }
            ?>
        </nav>
    </header>
    <main>
        <?php if (!isset($_SESSION['usuario'])): ?>
            <?php if (isset($_GET['error'])): ?>
                <p class="error">Usuario o contraseña incorrectos</p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required><br>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required><br>
                <button type="submit">Iniciar sesión</button>
            </form>
        <?php else: ?>
            <h2>Bienvenido, <?php echo $_SESSION['usuario']; ?>!</h2>
            <p>Usa el menú para gestionar tus finanzas personales.</p>
        <?php endif; ?>
    </main>
</body>

</html>