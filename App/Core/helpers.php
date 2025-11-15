<?php
/**
 * Archivo de funciones auxiliares globales
 * Compatible con PHP 8+ y arquitectura MVC del proyecto
 */

if (!function_exists('json_response')) {
    /**
     * Envía una respuesta JSON con el código HTTP deseado.
     * @param mixed $data  Datos a enviar (array, objeto, string, etc.)
     * @param int $status  Código HTTP (por defecto: 200)
     */
    function json_response($data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirige a otra ruta dentro del proyecto
     * @param string $path Ruta relativa (por ejemplo: '/login' o '/books')
     */
    function redirect(string $path): void {
        $base = '/proyecto_final/library_system_php_fixed/public';
        header('Location: ' . $base . $path);
        exit;
    }
}

if (!function_exists('view')) {
    /**
     * Carga una vista dentro del layout principal
     * @param string $view Nombre de la vista (por ejemplo 'books/index')
     * @param array $data  Variables a pasar a la vista
     */
    function view(string $view, array $data = []): void {
        extract($data);
        $base = '/proyecto_final/library_system_php_fixed/public';
        require __DIR__ . "/../../views/layout.php";
    }
}

if (!function_exists('asset')) {
    /**
     * Devuelve la URL completa de un recurso (CSS, JS, imágenes, etc.)
     * @param string $path Ruta relativa dentro de /public (por ejemplo 'css/style.css')
     * @return string
     */
    function asset(string $path): string {
        return '/proyecto_final/library_system_php_fixed/public/' . ltrim($path, '/');
    }
}

if (!function_exists('dd')) {
    /**
     * Debug rápido (dump and die)
     * @param mixed $var
     */
    function dd(mixed $var): void {
        echo '<pre style="background:#111;color:#0f0;padding:1rem;border-radius:8px;">';
        var_dump($var);
        echo '</pre>';
        exit;
    }
}
