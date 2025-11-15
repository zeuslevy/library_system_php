<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Config;
use PDO;
use Exception;

class Loan
{
    public static function create(int $user_id, int $book_id)
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                INSERT INTO loans (user_id, book_id, loan_date, fine)
                VALUES (:user_id, :book_id, CURDATE(), 0.00)
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':book_id' => $book_id
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            error_log("❌ Error en Loan::create → " . $e->getMessage());
            return false;
        }
    }

    public static function markReturn(int $loan_id): array
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT id, return_date, returned_at FROM loans WHERE id = :id");
            $stmt->execute([':id' => $loan_id]);
            $loan = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$loan) return ['error' => 'Préstamo no encontrado'];
            if (!empty($loan['returned_at'])) return ['error' => 'Este libro ya fue devuelto'];

            $today = new \DateTime();
            $returnDate = new \DateTime($loan['return_date']);
            $fine = 0.00;

            if ($today > $returnDate) {
                $daysLate = $returnDate->diff($today)->days;
                $fine = $daysLate * Config::$fine_per_day;
            }

            $update = $db->prepare("
                UPDATE loans
                SET returned_at = NOW(), fine = :fine
                WHERE id = :id
            ");
            $update->execute([':fine' => $fine, ':id' => $loan_id]);

            return ['fine' => $fine];
        } catch (Exception $e) {
            error_log("❌ Error en Loan::markReturn → " . $e->getMessage());
            return ['error' => 'Error interno al devolver el libro'];
        }
    }

    public static function getByUser(int $user_id): array
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT 
                    l.id, l.loan_date, l.return_date, l.returned_at, l.fine,
                    b.title, b.authors
                FROM loans l
                JOIN books b ON l.book_id = b.id
                WHERE l.user_id = :user_id
                ORDER BY l.loan_date DESC
            ");
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("❌ Error en Loan::getByUser → " . $e->getMessage());
            return [];
        }
    }

    public static function getAll(): array
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->query("
                SELECT 
                    l.id, u.name AS user_name, b.title AS book_title, 
                    l.loan_date, l.return_date, l.returned_at, l.fine
                FROM loans l
                JOIN users u ON l.user_id = u.id
                JOIN books b ON l.book_id = b.id
                ORDER BY l.loan_date DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("❌ Error en Loan::getAll → " . $e->getMessage());
            return [];
        }
    }
}
