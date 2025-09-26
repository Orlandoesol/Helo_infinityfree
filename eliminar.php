<?php
session_start();
include("conexion.php");

// Validar rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: listar.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit();
}

$id = intval($_GET['id']);

// Eliminar registro
$sql = "DELETE FROM registros WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: listar.php");
    exit();
} else {
    echo "Error al eliminar: " . $conn->error;
}

$conn->close();
?>