<?php
include("conexion.php");

// Sanitizar entradas
$nombre   = $conn->real_escape_string($_POST['nombre']);
$email    = $conn->real_escape_string($_POST['email']);
$telefono = $conn->real_escape_string($_POST['telefono']);
$mensaje  = $conn->real_escape_string($_POST['mensaje']);

// Insertar en la tabla (debes crearla antes en MySQL)
$sql = "INSERT INTO registros (nombre, email, telefono, mensaje, fecha) 
        VALUES ('$nombre', '$email', '$telefono', '$mensaje', NOW())";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('✅ Registro enviado con éxito. ¡Gracias por confiar en nosotros!'); window.location.href='index.php';</script>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
