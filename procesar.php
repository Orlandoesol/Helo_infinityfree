<?php
include("conexion.php");

// Sanitizar entradas
$nombre   = $conn->real_escape_string($_POST['nombre']);
$email    = $conn->real_escape_string($_POST['email']);
$telefono = $conn->real_escape_string($_POST['telefono']);
$mensaje  = $conn->real_escape_string($_POST['mensaje']);

// Insertar en la tabla
$sql = "INSERT INTO registros (nombre, email, telefono, mensaje, fecha) 
        VALUES ('$nombre', '$email', '$telefono', '$mensaje', NOW())";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <style>
    body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f5f5f5; }
    .popup {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(0,0,0,0.6);
      visibility: hidden;
      opacity: 0;
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .popup.active {
      visibility: visible;
      opacity: 1;
    }
    .popup-content {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      max-width: 400px;
      width: 90%;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      animation: fadeIn 0.4s ease;
    }
    .popup-content img {
      max-width: 100px; /* tamaño del logo */
      margin-bottom: 15px;
    }
    .popup-content h2 { margin: 0 0 15px; color: #28a745; }
    .popup-content p { margin: 0 0 20px; color:#333; }
    .popup-content button {
      padding: 10px 20px;
      background: #28a745;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    .popup-content button:hover {
      background: #218838;
    }
    @keyframes fadeIn {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body>

<?php if ($conn->query($sql) === TRUE): ?>
  <div id="popup" class="popup active">
    <div class="popup-content">
      <!-- Aquí va tu logo -->
      <img src="assets/images/logo.jpg" alt="Logo de la empresa">
      <h2>✅ Registro Exitoso</h2>
      <p>¡Gracias por confiar en nosotros!</p>
      <button onclick="cerrarPopup()">Aceptar</button>
    </div>
  </div>
<?php else: ?>
  <div id="popup" class="popup active">
    <div class="popup-content">
      <img src="assets/images/logo.jpg" alt="Logo de la empresa">
      <h2>❌ Error</h2>
      <p><?php echo "Ocurrió un error: " . $conn->error; ?></p>
      <button onclick="cerrarPopup()">Cerrar</button>
    </div>
  </div>
<?php endif; ?>

<script>
  function cerrarPopup() {
    document.getElementById("popup").classList.remove("active");
    window.location.href = "index.php"; // redirige después de cerrar
  }
</script>

</body>
</html>
<?php
$conn->close();
?>