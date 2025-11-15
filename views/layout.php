<?php
use App\Core\Session;

Session::start();
$user = Session::get('user') ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistema de Biblioteca</title>

<!-- Bootstrap & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- 💅 Estilos personalizados -->
  <style>
    body {
      background-color: #f8f9fa;
      font-family: "Inter", system-ui, sans-serif;
    }
    nav.navbar {
      background: linear-gradient(90deg, #0d6efd 0%, #0056b3 100%);
    }
    nav.navbar .nav-link, nav.navbar .navbar-brand {
      color: white !important;
      font-weight: 500;
    }
    nav.navbar .nav-link:hover {
      text-decoration: underline;
    }
    footer {
      background: #0d6efd;
      color: white;
      text-align: center;
      padding: 1rem 0;
      margin-top: 3rem;
      font-size: 0.9rem;
    }
    .content-wrapper {
      min-height: 75vh;
    }
  </style>
</head>

<body>
  <!-- 🧭 Barra de navegación -->
  <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="<?= $base ?>/">
        <i class="bi bi-book-half"></i> Biblioteca
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <?php if ($user): ?>
            <li class="nav-item"><a href="<?= $base ?>/" class="nav-link">Catálogo</a></li>
            <?php if (in_array($user['role'], ['admin', 'librarian'])): ?>
              <li class="nav-item"><a href="<?= $base ?>/dashboard" class="nav-link">Panel</a></li>
            <?php else: ?>
              <li class="nav-item"><a href="<?= $base ?>/dashboard" class="nav-link">Mis préstamos</a></li>
            <?php endif; ?>
            <li class="nav-item">
              <form method="POST" action="<?= $base ?>/auth/logout" class="d-inline">
                <button class="btn btn-sm btn-light ms-2" type="submit">
                  <i class="bi bi-box-arrow-right"></i> Salir
                </button>
              </form>
            </li>
          <?php else: ?>
            <li class="nav-item"><a href="<?= $base ?>/auth/login" class="nav-link">Ingresar</a></li>
            <li class="nav-item"><a href="<?= $base ?>/auth/register" class="nav-link">Registrarse</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- 📦 Contenedor principal -->
  <main class="container my-4 content-wrapper">
    <?php
      $viewFile = __DIR__ . '/' . $view . '.php';
      if (file_exists($viewFile)) {
        require $viewFile;
      } else {
        echo "<div class='alert alert-danger text-center mt-5'>
                <i class='bi bi-exclamation-triangle'></i> Vista no encontrada: <code>$view</code>
              </div>";
      }
    ?>
  </main>

  <!-- ⚙️ Pie de página -->
   <footer>
    © <?= date('Y') ?> Biblioteca | <a href="mailto:admin@biblioteca.com" class="text-decoration-none text-secondary">admin@biblioteca.com</a>
  </footer>

  <!-- 📦 Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

