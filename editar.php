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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = $_POST['name'] ?? '';
    $email    = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $text  = $_POST['text'] ?? '';

    $update_sql = "UPDATE registers
                   SET name = ?, email = ?, phone = ?, text = ?
                   WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $name, $email, $phone, $text, $id);

    if ($update_stmt->execute()) {
        header("Location: listar.php");
        exit();
    } else {
        echo "Error al actualizar: " . $conn->error;
    }
}

// Obtener datos actuales
$sql = "SELECT * FROM registers WHERE id=$id and deleted = '0'";
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
    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
    <input type="tel" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" required pattern="[0-9]+" minlength="7" maxlength="15">
    <textarea name="text" required maxlength="255"><?php echo htmlspecialchars($row['text']); ?></textarea>
    <button type="submit">Guardar Cambios</button>
  </form>
</body>
</html>
<?php $conn->close(); ?>