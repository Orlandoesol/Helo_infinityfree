<?php
include("conexion.php");

// Sanitizar y obtener entradas
$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$text  = trim($_POST['text'] ?? '');

// --- PREPARED STATEMENT ---
$sql = "INSERT INTO registers (name, email, phone, text, date) 
        VALUES (?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $phone, $text);

if ($stmt->execute()) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8">
      <title>Registro Exitoso</title>
      <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:0; }
        .popup {
          position: fixed; top: 0; left: 0; right: 0; bottom: 0;
          display: flex; align-items: center; justify-content: center;
          background: rgba(0,0,0,0.6);
        }
        .popup-content {
          background: #fff; padding: 30px; border-radius: 12px; text-align: center;
          max-width: 400px; width: 90%; box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .popup-content img { max-width: 100px; margin-bottom: 15px; }
        .popup-content h2 { color: #28a745; margin: 0 0 15px; }
        .popup-content p { margin: 0 0 20px; }
        .popup-content button {
          padding: 10px 20px; background: #28a745; color: white;
          border: none; border-radius: 8px; cursor: pointer;
        }
        .popup-content button:hover { background: #218838; }
      </style>
    </head>
    <body>
      <div class="popup">
        <div class="popup-content">
          <img src="assets/images/logo.jpg" alt="Logo de la empresa">
          <h2>✅ Registro Exitoso</h2>
          <p>¡Gracias por confiar en nosotros!</p>
          <button onclick="window.location.href='index.php'">Aceptar</button>
        </div>
      </div>
    </body>
    </html>
    <?php
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>