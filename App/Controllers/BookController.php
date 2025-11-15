<?php
namespace App\Controllers;

use App\Models\Book;

class BookController
{
    /**
     * Mostrar catálogo principal de libros
     */
    public function index()
    {
        $books = Book::all();
        $view = 'books/index'; // ← cambiamos 'home' por la vista real del catálogo
        $base = '/proyecto_final/library_system_php_fixed/public';
        require __DIR__ . '/../../views/layout.php';
    }

    /**
     * Buscar libros por AJAX
     */
    public function searchAjax()
    {
        header('Content-Type: application/json; charset=utf-8');
        $q = trim($_GET['q'] ?? '');
        header('Content-Type: application/json');

        if ($q === '') {
            echo json_encode(['results' => []]);
            return;
    }

    $results = \App\Models\Book::search($q);

    // Empaquetamos correctamente para que coincida con el JS
    echo json_encode(['results' => $results], JSON_UNESCAPED_UNICODE);
    }

}