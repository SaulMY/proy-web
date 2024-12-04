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

    // Obtener datos de ingresos y gastos por día del mes actual
    $ingresos = $con->query("SELECT DATE(fecha) AS dia, SUM(monto) AS total 
                             FROM movimientos 
                             WHERE user_id = '$user_id' AND tipo = 'ingreso' 
                             AND MONTH(fecha) = MONTH(CURRENT_DATE()) 
                             AND YEAR(fecha) = YEAR(CURRENT_DATE()) 
                             GROUP BY dia");

    $gastos = $con->query("SELECT DATE(fecha) AS dia, SUM(monto) AS total 
                           FROM movimientos 
                           WHERE user_id = '$user_id' AND tipo = 'gasto' 
                           AND MONTH(fecha) = MONTH(CURRENT_DATE()) 
                           AND YEAR(fecha) = YEAR(CURRENT_DATE()) 
                           GROUP BY dia");

    // Totales acumulados
    $totalIngresos = $con->query("SELECT SUM(monto) AS total 
                                  FROM movimientos 
                                  WHERE user_id = '$user_id' AND tipo = 'ingreso' 
                                  AND MONTH(fecha) = MONTH(CURRENT_DATE()) 
                                  AND YEAR(fecha) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'] ?? 0;

    $totalGastos = $con->query("SELECT SUM(monto) AS total 
                                FROM movimientos 
                                WHERE user_id = '$user_id' AND tipo = 'gasto' 
                                AND MONTH(fecha) = MONTH(CURRENT_DATE()) 
                                AND YEAR(fecha) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'] ?? 0;

    // Formatear datos para Chart.js
    $labels = [];
    $dataIngresos = [];
    $dataGastos = [];

    // Rellenar datos de ingresos
    while ($row = $ingresos->fetch_assoc()) {
      $labels[] = $row['dia'];
      $dataIngresos[] = $row['total'];
    }

    // Rellenar datos de gastos
    while ($row = $gastos->fetch_assoc()) {
      $key = array_search($row['dia'], $labels);
      if ($key !== false) {
        $dataGastos[$key] = $row['total'];
      } else {
        $labels[] = $row['dia'];
        $dataIngresos[] = 0; // Para mantener el índice alineado
        $dataGastos[] = $row['total'];
      }
    }

    // Asegurar que las listas sean del mismo tamaño
    foreach ($labels as $index => $label) {
      if (!isset($dataIngresos[$index])) $dataIngresos[$index] = 0;
      if (!isset($dataGastos[$index])) $dataGastos[$index] = 0;
    }

    // Ordenar las listas según la fecha
    array_multisort($labels, SORT_ASC, $dataIngresos, $dataGastos);

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
  <title>Gráficos</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="CSS/Styles.css"> <!-- Enlace al archivo CSS -->
</head>
<body>
    <header>
        <h1>Gráficos de Ingresos vs. Gastos</h1>
        <nav>
          <a href="agregar.php">Agregar Ingresos/Gastos</a>
          <a href="balance.php">Ver Balance</a>
          <a href="graficos.php">Gráficos</a>
          <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>
    <main>
      <h2>Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h2>

      <!-- Gráfico de líneas -->
      <h3>Ingresos vs. Gastos por Día</h3>
      <canvas id="lineChart" width="400" height="200"></canvas>

      <!-- Gráfico de barras -->
      <h3>Totales Acumulados del Mes</h3>
      <canvas id="barChart" width="400" height="200"></canvas>
    </main>
    <script>
      // Gráfico de líneas
      const lineCtx = document.getElementById('lineChart').getContext('2d');
      new Chart(lineCtx, {
          type: 'line',
          data: {
              labels: <?php echo json_encode($labels); ?>,
              datasets: [
                  {
                      label: 'Ingresos',
                      data: <?php echo json_encode($dataIngresos); ?>,
                      borderColor: 'rgba(75, 192, 192, 1)',
                      backgroundColor: 'rgba(75, 192, 192, 0.2)',
                      borderWidth: 2,
                      fill: true
                  },
                  {
                      label: 'Gastos',
                      data: <?php echo json_encode($dataGastos); ?>,
                      borderColor: 'rgba(255, 99, 132, 1)',
                      backgroundColor: 'rgba(255, 99, 132, 0.2)',
                      borderWidth: 2,
                      fill: true
                  }
              ]
          },
          options: {
              responsive: true,
              scales: {
                  x: {
                      title: { display: true, text: 'Días del Mes' }
                  },
                  y: {
                      title: { display: true, text: 'Monto ($)' },
                      beginAtZero: true
                  }
              }
          }
      });

      // Gráfico de barras
      const barCtx = document.getElementById('barChart').getContext('2d');
      new Chart(barCtx, {
          type: 'bar',
          data: {
              labels: ['Ingresos', 'Gastos'],
              datasets: [
                  {
                      label: 'Totales del Mes',
                      data: [<?php echo $totalIngresos; ?>, <?php echo $totalGastos; ?>],
                      backgroundColor: ['rgba(75, 192, 192, 0.8)', 'rgba(255, 99, 132, 0.8)'],
                      borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                      borderWidth: 1
                  }
              ]
          },
          options: {
              responsive: true,
              scales: {
                  y: {
                      beginAtZero: true,
                      title: { display: true, text: 'Monto ($)' }
                  }
              }
          }
      });
    </script>
</body>
</html>

<?php
// Cerrar conexión
$con->close();
?>