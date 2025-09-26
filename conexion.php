<?php
$host = "sql204.infinityfree.com";   // normalmente localhost en hosting compartido
$user = "if0_40003724";  // tu usuario MySQL
$pass = "q5dhg3QuTpKBAPQ"; // tu contraseña MySQL
$db   = "if0_40003724_helo";   // nombre de la base de datos

$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
