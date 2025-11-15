<?php
use App\Core\Csrf;
use App\Core\Session;

require_once __DIR__ . '/../app/core/bootstrap.php';

// Inicia sesión de forma segura
Session::start();

// Generar token de prueba
$token = Csrf::token('diagnostico');

// Datos de diagnóstico
$sessionId = session_id();
$sessionPath = ini_get('session.save_path');
$sessionFile = $sessionPath . DIRECTORY_SEPARATOR . 'sess_' . $sessionId;
$cookie = $_COOKIE['PHPSESSID'] ?? '(no enviada)';
$exists = file_exists($sessionFile) ? '✅ Sí' : '❌ No';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Diagnóstico CSRF y Sesiones</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
  <div class="card shadow-sm p-4">
    <h3 class="text-center text-primary mb-4">🔍 Diagnóstico de Sesiones y CSRF</h3>

    <table class="table table-bordered table-striped">
      <tr><th>session_id()</th><td><code><?= htmlspecialchars($sessionId) ?></code></td></tr>
      <tr><th>Ruta de sesiones (session.save_path)</th><td><?= htmlspecialchars($sessionPath) ?></td></tr>
      <tr><th>Archivo de sesión existe</th><td><?= $exists ?> (<?= htmlspecialchars($sessionFile) ?>)</td></tr>
      <tr><th>Cookie PHPSESSID recibida</th><td><?= htmlspecialchars($cookie) ?></td></tr>
      <tr><th>Token CSRF generado</th><td><code><?= htmlspecialchars($token) ?></code></td></tr>
      <tr><th>Contenido actual de $_SESSION['_csrf']</th><td><pre><?= print_r($_SESSION['_csrf'] ?? '(vacío)', true) ?></pre></td></tr>
    </table>

    <p class="text-muted small text-center">
      🔁 Actualiza la página (F5). Si el <b>token CSRF</b> o el <b>session_id</b> cambian, la sesión no se conserva correctamente.
    </p>
  </div>
</div>
</body>
</html>
