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
$sql = "UPDATE registers SET DELETED = '1' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: listar.php");
    exit();
} else {
    echo "Error al eliminar: " . $conn->error;
}

$conn->close();
?>