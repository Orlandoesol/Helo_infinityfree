<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eficiencia Energética Empresarial</title>
  <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600|Open+Sans:300,400&display=swap" rel="stylesheet">
  <style>
    body {margin:0;font-family:'Open Sans',sans-serif;color:#333;line-height:1.6;background-color:#f7f9fc;}
    header {position:relative;height:90vh;color:white;text-align:center;display:flex;align-items:center;justify-content:center;overflow:hidden;}
    header::before {content:"";position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1;}
    header img.bg {position:absolute;top:50%;left:50%;width:100%;height:100%;object-fit:cover;transform:translate(-50%,-50%);z-index:0;}
    header .hero-content {position:relative;z-index:2;max-width:800px;padding:0 20px;}
    header h1 {font-family:'Montserrat',sans-serif;font-size:3rem;margin-bottom:20px;font-weight:600;}
    header p {font-size:1.3rem;margin-bottom:30px;}
    .cta-btn {font-family:'Montserrat',sans-serif;background-color:#28a745;color:#fff;padding:15px 30px;font-size:1.1rem;text-decoration:none;border-radius:50px;transition:0.3s ease;display:inline-block;}
    .cta-btn:hover {background-color:#218838;transform:scale(1.05);}
    .container {max-width:900px;margin:60px auto;padding:0 20px;}
    .features {display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:40px;}
    .feature-card {background:#fff;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.1);overflow:hidden;display:flex;flex-direction:column;transition:0.3s ease;}
    .feature-card:hover {transform:translateY(-5px);}
    .feature-card img {width:100%;height:180px;object-fit:cover;}
    .feature-card .feature-body {padding:20px;flex:1;}
    .feature-card h3 {margin-top:0;font-family:'Montserrat',sans-serif;font-size:1.4rem;color:#28a745;}
    .feature-card p {font-size:1rem;color:#666;}
    .registration {margin-top: 60px;background:#ffffff;padding:40px 20px;border-radius:8px;box-shadow:0 4px 15px rgba(0,0,0,0.1);margin-bottom:60px;}
    .registration h2 {font-family:'Montserrat',sans-serif;text-align:center;font-size:2rem;color:#333;margin-bottom:30px;}
    .registration form {display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .registration form .full-width {grid-column:1/-1;}
    .registration input,.registration textarea {padding:15px;border:1px solid #ccc;border-radius:5px;font-size:1rem;transition:border-color 0.3s ease;}
    .registration input:focus,.registration textarea:focus {border-color:#28a745;outline:none;}
    .registration button {grid-column:1/-1;padding:18px;background-color:#28a745;color:white;font-size:1.2rem;border:none;border-radius:50px;cursor:pointer;transition:0.3s ease;}
    .registration button:hover {background-color:#218838;transform:scale(1.02);}
    footer {text-align:center;padding:30px 20px;background-color:#2f2f2f;color:#bbb;font-size:0.9rem;}
    footer a {color:#ddd;text-decoration:none;}
    footer a:hover {color:#fff;}
    @media (max-width:768px){header h1{font-size:2.2rem;}header p{font-size:1.1rem;}.registration form{grid-template-columns:1fr;}}
  </style>
</head>
<body>
  <header>
    <!-- Imagen de eficiencia energética empresarial (sin paneles solares) -->
    <img src="https://cdn1.corresponsables.com/wp-content/uploads/2023/08/eficiencia_energetica_0.png" 
         alt="Eficiencia energética en empresas" class="bg">
    <div class="hero-content">
      <h1>Reduce hasta un 35% tus costos de energía</h1>
      <p>Soluciones inteligentes para empresas que buscan ahorrar y ser más sostenibles sin comprometer la productividad.</p>
      <a href="#registro" class="cta-btn">Solicita tu diagnóstico gratuito</a>
      <a href="login.php" class="cta-btn" style="background-color:#007bff; margin-left:10px;">
      Acceso Administrador
    </a>
    </div>
  </header>

  <div class="container">
    <!-- Beneficios -->
    <section class="features">
      <div class="feature-card">
        <img src="https://citsolar.mx/wp-content/uploads/2024/11/image-17.png" alt="Optimización de consumo">
        <div class="feature-body">
          <h3>Monitoreo inteligente</h3>
          <p>Identificamos ineficiencias en tiempo real para que tomes decisiones informadas y reduzcas tu factura energética.</p>
        </div>
      </div>
      <div class="feature-card">
        <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=800" alt="Tecnología avanzada">
        <div class="feature-body">
          <h3>Gestión automatizada</h3>
          <p>Implementamos sistemas que ajustan automáticamente el uso de energía, maximizando eficiencia sin afectar operaciones.</p>
        </div>
      </div>
      <div class="feature-card">
        <img src="https://climnatur.com/wp-content/uploads/2023/10/Eficiencia-energetica.-ClimNatur.jpeg" alt="Eficiencia operativa">
        <div class="feature-body">
          <h3>Ahorro sostenible</h3>
          <p>Obtén resultados inmediatos en reducción de costos y fortalece la imagen de tu empresa como líder en sostenibilidad.</p>
        </div>
      </div>
    </section>

    <!-- Formulario -->
    <div class="registration" id="registro">
      <h2>Regístrate y comienza a ahorrar</h2>
      <form action="procesar.php" method="POST">
        <!-- Nombre solo letras -->
        <input type="text"
              name="name"
              placeholder="Nombre completo"
              required
              pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
              title="Solo se permiten letras y espacios">
        <!-- Email formato válido -->
        <input type="email"
              name="email"
              placeholder="Correo electrónico"
              required>
        <!-- Teléfono solo números -->
        <input type="tel"
              name="phone"
              placeholder="Teléfono/Celular"
              required
              pattern="[0-9]+"
              minlength="7"
              maxlength="15"
              title="Ingrese solo números, mínimo 7 y máximo 15">
        <!-- Mensaje con límite de caracteres -->
        <textarea name="text"
                  rows="4"
                  placeholder="¿Qué esperas optimizar en tu empresa?"
                  class="full-width"
                  maxlength="255"
                  required></textarea>
        <button type="submit">¡Quiero optimizar mi energía!</button>
      </form>
    </div>
  </div>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Energía Inteligente S.A.S.</p>
    <p><a href="#">Política de privacidad</a> | <a href="#">Términos y condiciones</a></p>
  </footer>
</body>
</html>
