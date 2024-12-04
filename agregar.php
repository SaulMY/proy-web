<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos/Ingresos</title>
    <link rel="stylesheet" href="CSS/Styles.css"> <!-- Enlace al archivo CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <header>
        <h1>Bienvenido a tu registro personal</h1>
        <nav>
            <a href="agregar.php">Agregar Ingresos/Gastos</a>
            <a href="balance.php">Ver Balance</a>
            <a href="graficos.php">Gráficos</a>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>
    <h2>Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h2>
    <main>
        <form action="" method="POST">
            <div class="form-row align-items-center">
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" required>
                    <option value="ingreso">Ingreso</option>
                    <option value="gasto">Gasto</option>
                </select>
                <br>
                <label for="monto">Monto:</label>
                <input type="number" step="0.01" name="monto" id="monto" required>
                <br>
                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" required></textarea>
                <br>
                <button type="submit" name="registrar">Registrar Movimiento</button>
            </div>
        </form>
        <div>
            
            <?php
            // Conexión a la base de datos
            $con = new mysqli('localhost', 'root', '', 'BudGet');

            // Verificar conexión
            if ($con->connect_error) {
                die("Error de conexión: " . $con->connect_error);
            }

            // Procesar formulario
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
                $tipo = $con->real_escape_string($_POST['tipo']);
                $monto = $con->real_escape_string($_POST['monto']);
                $descripcion = $con->real_escape_string($_POST['descripcion']);

                // Obtener el ID del usuario actual (asumiendo que está en la sesión)
                $usuario = $_SESSION['usuario'];
                $result = $con->query("SELECT id FROM users WHERE username='$usuario'");
                if ($result && $row = $result->fetch_assoc()) {
                    $user_id = $row['id'];

                    // Insertar movimiento
                    $sql = "INSERT INTO movimientos (user_id, tipo, monto, descripcion) 
                            VALUES ('$user_id', '$tipo', '$monto', '$descripcion')";
                    if ($con->query($sql) === TRUE) {
                        echo "<p>Movimiento registrado correctamente.</p>";
                    } else {
                        echo "<p>Error al registrar el movimiento: " . $con->error . "</p>";
                    }
                } else {
                    echo "<p>Error: Usuario no encontrado.</p>";
                }
            }

            // Mostrar los últimos 10 movimientos
            echo '<br>';
            echo '<br>';
            echo "<h3>Últimos 10 movimientos</h3>";
            $usuario = $_SESSION['usuario'];
            $result = $con->query("SELECT id FROM users WHERE username='$usuario'");
            if ($result && $row = $result->fetch_assoc()) {
                $user_id = $row['id'];
                $movimientos = $con->query("SELECT tipo, monto, descripcion, fecha 
                                            FROM movimientos 
                                            WHERE user_id = '$user_id' 
                                            ORDER BY fecha DESC LIMIT 10");

                if ($movimientos->num_rows > 0) {
                    echo "<table class='table table-striped'>
                            <thead class=''>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th>Descripción</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>";
                    while ($mov = $movimientos->fetch_assoc()) {
                        echo "
                            <tbody>
                            <tr>
                                <td>" . htmlspecialchars($mov['tipo']) . "</td>
                                <td>" . htmlspecialchars($mov['monto']) . "</td>
                                <td>" . htmlspecialchars($mov['descripcion']) . "</td>
                                <td>" . htmlspecialchars($mov['fecha']) . "</td>
                            </tr>
                            </tbody>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No hay movimientos registrados.</p>";
                }
            } else {
                echo "<p>Error: Usuario no encontrado.</p>";
            }

            // Cerrar conexión
            $con->close();
            ?>
        
        </div>
    </main>
</body>
</html>