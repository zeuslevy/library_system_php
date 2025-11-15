<?php
declare(strict_types=1);

// ---------------------------------------------
// Bootstrap — Configuración inicial del sistema
// ---------------------------------------------

// 🧩 Configuración de sesión (solo si no está activa)
if (session_status() === PHP_SESSION_NONE) {
    $params = session_get_cookie_params();

    session_set_cookie_params([
        'lifetime' => 60 * 60 * 2, // 2 horas
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// ---------------------------------------------
// Autocarga de clases del namespace App\...
// ---------------------------------------------
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// ---------------------------------------------
// Inclusión de archivos base
// ---------------------------------------------
require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/Csrf.php';
require_once __DIR__ . '/../helpers.php';
