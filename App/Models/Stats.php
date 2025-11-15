<?php
namespace App\Models;
use App\Core\Database;
use PDO;

class Stats {
    public static function getSummary(): array {
        $db = Database::getInstance();

        $totalBooks = $db->query("SELECT COUNT(*) FROM books")->fetchColumn();
        $totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $activeLoans = $db->query("SELECT COUNT(*) FROM loans WHERE date_return IS NULL")->fetchColumn();
        $totalFines = 0; // puedes agregar cálculo real después

        return [
            'books' => $totalBooks,
            'users' => $totalUsers,
            'loans' => $activeLoans,
            'fines' => $totalFines,
        ];
    }
}
