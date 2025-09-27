<?php
session_start();
include 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Rol del usuario
$rol = $_SESSION['role'] ?? 'asesor';

// Consulta de registros
$sql = "SELECT * FROM registers where deleted = '0' ORDER BY date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <meta charset="UTF-8">
    <title>Listado de Registros</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        header img {
            height: 60px;
            margin-right: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .acciones a {
            margin: 0 5px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            color: #fff;
        }
        .editar {
            background-color: #3498db;
        }
        .eliminar {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>
    <header>
        <img src="assets/images/logo.jpg" alt="Logo de la empresa">
        <h2>Listado de Registros</h2>
    </header>

    <p>Bienvenido <b><?php echo htmlspecialchars($_SESSION['user']); ?></b> (Rol: <?php echo htmlspecialchars($rol); ?>)</p>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Mensaje</th>
            <th>Fecha</th>
            <?php if ($rol === 'admin') echo "<th>Acciones</th>"; ?>
        </tr>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['text']); ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <?php if ($rol === 'admin'): ?>
                        <td class="acciones">
                            <a href="editar.php?id=<?php echo $row['id']; ?>" class="editar">Editar</a>
                            <a href="eliminar.php?id=<?php echo $row['id']; ?>" class="eliminar" onclick="return confirm('¿Seguro de eliminar este registro?')">Eliminar</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="<?php echo ($rol === 'admin') ? 7 : 6; ?>">No hay registros</td></tr>
        <?php endif; ?>
    </table>

    <br>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>