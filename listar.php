<?php
// listado.php
include("conexion.php");

// Consulta de registros
$sql = "SELECT id, nombre, email, telefono, mensaje, fecha FROM registros ORDER BY fecha DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Listado de Registros - Eficiencia Energética</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600|Open+Sans:300,400&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Open Sans', sans-serif; background:#f7f9fc; margin:0; padding:0; }
    header { background:#28a745; padding:20px; color:#fff; text-align:center; }
    header h1 { margin:0; font-family:'Montserrat',sans-serif; }
    .container { max-width:1100px; margin:40px auto; padding:0 20px; }
    table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08); }
    th, td { padding:14px 16px; text-align:left; }
    th { background:#28a745; color:#fff; font-family:'Montserrat',sans-serif; font-weight:600; }
    tr:nth-child(even) { background:#f3f3f3; }
    tr:hover { background:#e9f7ef; }
    td { color:#333; }
    .empty { text-align:center; padding:40px; color:#666; }
    .back-link { display:inline-block; margin-bottom:20px; color:#28a745; text-decoration:none; font-weight:600; }
    .back-link:hover { text-decoration:underline; }
    footer { text-align:center; padding:20px; margin-top:40px; color:#777; font-size:0.9rem; }
  </style>
</head>
<body>
  <header>
    <h1>Personas Registradas</h1>
  </header>

  <div class="container">
    <a href="index.php" class="back-link">← Volver a la landing</a>

    <?php if ($result && $result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Mensaje</th>
            <th>Fecha Registro</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row["id"]); ?></td>
              <td><?php echo htmlspecialchars($row["nombre"]); ?></td>
              <td><?php echo htmlspecialchars($row["email"]); ?></td>
              <td><?php echo htmlspecialchars($row["telefono"]); ?></td>
              <td><?php echo htmlspecialchars($row["mensaje"]); ?></td>
              <td><?php echo htmlspecialchars($row["fecha"]); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty">No hay registros todavía.</div>
    <?php endif; ?>
  </div>

  <footer>
    &copy; <?php echo date("Y"); ?> Energía Inteligente S.A.S.
  </footer>
</body>
</html>
<?php
$conn->close();
?>
