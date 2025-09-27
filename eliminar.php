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

// Eliminar registro
$update_sql = "UPDATE registers SET deleted = '1' WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $id);

if ($update_stmt->execute()) {
    header("Location: listar.php");
    exit();
} else {
    echo "Error al eliminar: " . $update_stmt->error;
}

$update_stmt->close();
$conn->close();
?>