<?php
namespace App\Controllers;

use App\Core\Session;
use App\Core\Database;

class DashboardController
{
    /**
     * Muestra el panel de control del sistema:
     * - Estudiantes → sus préstamos personales
     * - Admin/Bibliotecarios → estadísticas generales
     */
    public function index()
    {
        Session::start();
        $base = '/proyecto_final/library_system_php_fixed/public';

        // 🔒 Verificar sesión activa
        $user = Session::get('user');
        if (!$user) {
            header("Location: $base/auth/login");
            exit;
        }

        // 📚 Conexión
        $db = Database::getInstance();

        // 👩‍🎓 Si el usuario es estudiante → mostrar préstamos personales
        if ($user['role'] === 'student') {
            $stmt = $db->prepare("
                SELECT 
                    l.id,
                    b.title,
                    b.authors,
                    l.loan_date,
                    l.return_date,
                    l.returned_at,
                    l.fine
                FROM loans l
                JOIN books b ON l.book_id = b.id
                WHERE l.user_id = :uid
                ORDER BY l.loan_date DESC
            ");
            $stmt->execute([':uid' => $user['id']]);
            $myLoans = $stmt->fetchAll();

            $view = 'dashboard/student';
            require __DIR__ . '/../../views/layout.php';
            return;
        }

        // 🧩 Si es bibliotecario o administrador → estadísticas generales
        $totalBooks = $db->query("SELECT COUNT(*) FROM books")->fetchColumn();
        $totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $totalLoans = $db->query("SELECT COUNT(*) FROM loans")->fetchColumn();
        $activeLoans = $db->query("SELECT COUNT(*) FROM loans WHERE returned_at IS NULL")->fetchColumn();

        // 📈 Datos para gráfico (últimos 6 meses)
        $chartData = $db->query("
            SELECT DATE_FORMAT(loan_date, '%Y-%m') AS mes, COUNT(*) AS cantidad
            FROM loans
            GROUP BY mes
            ORDER BY mes DESC
            LIMIT 6
        ")->fetchAll();

        $chartLabels = array_column(array_reverse($chartData), 'mes');
        $chartValues = array_column(array_reverse($chartData), 'cantidad');

        // 📄 Cargar vista principal del panel
        $view = 'dashboard/index';
        require __DIR__ . '/../../views/layout.php';
    }
}
