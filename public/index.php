<?php
// FRONT CONTROLLER — SISTEMA DE GESTIÓN DE BIBLIOTECA
// ----------------------------------------------------

// Inicializa el entorno de la aplicación
require_once __DIR__ . '/../app/core/bootstrap.php';

// Importa los controladores
use App\Controllers\BookController;
use App\Controllers\LoanController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;


// Define la base del proyecto (ajústala si cambias la carpeta)
$base = '/proyecto_final/library_system_php_fixed/public';

// Obtiene la URI solicitada
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace($base, '', $uri);
$path = rtrim($path, '/'); // quita "/" final si existe
$method = $_SERVER['REQUEST_METHOD'];
 
// --------------------------------------------
// RUTEADOR PRINCIPAL
// --------------------------------------------
try {
    switch (true) {

        // 🏠 Página principal → Catálogo de libros
        case ($path === '' || $path === '/') && $method === 'GET':
            (new BookController())->index();
            break;

        // 📊 Panel administrativo o de usuario
        case $path === '/dashboard' && $method === 'GET':
            if (class_exists(DashboardController::class)) {
                (new DashboardController())->index();
            } else {
                echo "<h2 style='text-align:center;color:#6c757d'>Panel aún no disponible.</h2>";
            }
            break;

        // 📚 Búsqueda AJAX de libros
        case $path === '/books/search' && $method === 'GET':
            (new BookController())->searchAjax();
            break;

        // 💼 Registrar préstamo
        case $path === '/loans/create' && $method === 'POST':
            (new LoanController())->create();
            break;

        // 🔁 Registrar devolución
        case $path === '/loans/return' && $method === 'POST':
            (new LoanController())->return();
            break;

        // 🔐 Login (mostrar formulario)
        case $path === '/auth/login' && $method === 'GET':
            (new AuthController())->login();
            break;

        // 🔐 Login (procesar)
        case $path === '/auth/login' && $method === 'POST':
            (new AuthController())->login();
            break;

        // 🧍 Registro (mostrar formulario)
        case $path === '/auth/register' && $method === 'GET':
            (new AuthController())->register();
            break;

        // 🧍 Registro (procesar envío)
        case $path === '/auth/register' && $method === 'POST':
            (new AuthController())->register();
            break;

        // 🚪 Cerrar sesión
        case $path === '/auth/logout' && $method === 'POST':
            (new AuthController())->logout();
            break;

        // 🧩 Página no encontrada
        default:
            http_response_code(404);
            echo "<div style='font-family:Inter,sans-serif;text-align:center;padding:80px'>
                    <h1 style='color:#dc3545'>404 — Página no encontrada</h1>
                    <p style='color:#6c757d'>Ruta: <code>" . htmlspecialchars($path) . "</code></p>
                    <a href='{$base}/' style='color:#0d6efd;text-decoration:none'>← Volver al catálogo</a>
                  </div>";
    }

} catch (Throwable $e) {
    // 🧨 Manejo de errores global
    http_response_code(500);
    echo "<div style='font-family:Inter,sans-serif;text-align:center;margin-top:3rem'>
            <h1 style='color:#dc3545'>Error interno del servidor</h1>
            <pre style='color:#6c757d;margin-top:1rem;font-size:15px'>"
            . htmlspecialchars($e->getMessage()) .
            "</pre>
          </div>";
}
