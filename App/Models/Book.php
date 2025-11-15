<?php
namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;

class Book
{
    /**
     * 📚 Obtiene todos los libros
     */
    public static function all(): array
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->query("SELECT * FROM books ORDER BY title ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("❌ Error en Book::all → " . $e->getMessage());
            return [];
        }
    }

    /**
     * 🔍 Búsqueda rápida (AJAX)
     */
    public static function search(string $query): array
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT * FROM books
                WHERE title LIKE :q OR authors LIKE :q OR isbn LIKE :q
                ORDER BY title ASC
            ");
            $stmt->execute([':q' => "%$query%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['results' => $results];
        } catch (Exception $e) {
            error_log("❌ Error en Book::search → " . $e->getMessage());
            return ['results' => []];
        }
    }

    /**
     * 🔽 Disminuye las copias disponibles (al prestar)
     * ⚠️ Nota: ya existe un trigger que hace esto, así que solo es decorativo.
     */
    public static function decrementAvailable(int $book_id): bool
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                UPDATE books 
                SET copies_available = GREATEST(copies_available - 1, 0)
                WHERE id = :id
            ");
            return $stmt->execute([':id' => $book_id]);
        } catch (Exception $e) {
            error_log("❌ Error en Book::decrementAvailable → " . $e->getMessage());
            return false;
        }
    }

    /**
     * 🔼 Incrementa las copias disponibles (al devolver)
     */
    public static function incrementAvailable(int $book_id): bool
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                UPDATE books 
                SET copies_available = LEAST(copies_available + 1, copies_total)
                WHERE id = :id
            ");
            return $stmt->execute([':id' => $book_id]);
        } catch (Exception $e) {
            error_log("❌ Error en Book::incrementAvailable → " . $e->getMessage());
            return false;
        }
    }
}

