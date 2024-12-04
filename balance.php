<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Conexión a la base de datos
$con = new mysqli('localhost', 'root', '', 'BudGet');

// Verificar conexión
if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error);
}

// Obtener el usuario actual
$usuario = $_SESSION['usuario'];
$result = $con->query("SELECT id FROM users WHERE username='$usuario'");

if ($result && $row = $result->fetch_assoc()) {
    $user_id = $row['id'];

    // Calcular el total de ingresos y gastos
    $ingresos = $con->query("SELECT SUM(monto) AS total FROM movimientos WHERE user_id = '$user_id' AND tipo = 'ingreso'");
    $gastos = $con->query("SELECT SUM(monto) AS total FROM movimientos WHERE user_id = '$user_id' AND tipo = 'gasto'");

    $totalIngresos = $ingresos->fetch_assoc()['total'] ?? 0;
    $totalGastos = $gastos->fetch_assoc()['total'] ?? 0;

    // Calcular el balance
    $balance = $totalIngresos - $totalGastos;
} else {
    echo "<p>Error: Usuario no encontrado.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance</title>
    <link rel="stylesheet" href="CSS/Styles.css"> <!-- Enlace al archivo CSS -->
</head>
<body>
    <header>
        <h1>Balance Personal</h1>
        <nav>
            <a href="agregar.php">Agregar Ingresos/Gastos</a>
            <a href="balance.php">Ver Balance</a>
            <a href="graficos.php">Gráficos</a>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>
    <main>
        <h2>Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h2>
        <h3>Tu Balance</h3>
        <p><strong>Total de Ingresos:</strong> $<?php echo number_format($totalIngresos, 2); ?></p>
        <p><strong>Total de Gastos:</strong> $<?php echo number_format($totalGastos, 2); ?></p>
        <p><strong>Balance:</strong> $<?php echo number_format($balance, 2); ?></p>

        <?php if ($balance < 0): ?>
            <p style="color: red;">Tu balance es negativo. ¡Revisa tus gastos!</p>
        <?php elseif ($balance > 0): ?>
            <p style="color: green;">¡Buen trabajo! Tu balance es positivo.</p>
        <?php else: ?>
            <p style="color: orange;">Tu balance es neutro.</p>
        <?php endif; ?>
    </main>
</body>
</html>

<?php
// Cerrar conexión
$con->close();
?>