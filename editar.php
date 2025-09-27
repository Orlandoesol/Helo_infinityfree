<?php
session_start();
include("conexion.php");

// Validar rol
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: listar.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit();
}

$id = intval($_GET['id']);

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = $conn->real_escape_string($_POST['nombre']);
    $email    = $conn->real_escape_string($_POST['email']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $mensaje  = $conn->real_escape_string($_POST['mensaje']);

    $sql = "UPDATE registros 
            SET nombre='$nombre', email='$email', telefono='$telefono', mensaje='$mensaje'
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: listar.php");
        exit();
    } else {
        echo "Error al actualizar: " . $conn->error;
    }
}

// Obtener datos actuales
$sql = "SELECT * FROM registros WHERE id=$id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Registro no encontrado.";
    exit();
}

$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Registro</title>
  <style>
    form { max-width: 400px; margin: 40px auto; display: flex; flex-direction: column; gap: 10px; }
    input, textarea, button { padding: 10px; font-size: 1rem; }
    button { background: #28a745; color: white; border: none; cursor: pointer; border-radius: 6px; }
    button:hover { background: #218838; }
  </style>
</head>
<body>
  <h2>Editar Registro</h2>
  <form method="POST">
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($row['name']); ?>" required>
    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
    <input type="tel" name="telefono" value="<?php echo htmlspecialchars($row['phone']); ?>" required pattern="[0-9]+" minlength="7" maxlength="15">
    <textarea name="mensaje" required maxlength="255"><?php echo htmlspecialchars($row['text']); ?></textarea>
    <button type="submit">Guardar Cambios</button>
  </form>
</body>
</html>
<?php $conn->close(); ?>