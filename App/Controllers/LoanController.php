<?php
namespace App\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Core\Csrf;
use App\Core\Session;

class LoanController
{
    /**
     * 📘 Crear nuevo préstamo
     */
    public function create()
    {
        Session::start();

        if (!\App\Core\Csrf::validate($_POST['_csrf'] ?? null, 'loan')) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'msg' => 'Token CSRF inválido (loan)']);
            return;
        }

        $user_id = intval($_POST['user_id'] ?? 0);
        $book_id = intval($_POST['book_id'] ?? 0);

        if (!$user_id || !$book_id) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msg' => 'Datos incompletos']);
            return;
        }

        $loanId = Loan::create($user_id, $book_id);

        if (!$loanId) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'msg' => 'Error al registrar el préstamo.']);
            return;
        }

        echo json_encode(['ok' => true, 'msg' => 'Préstamo registrado correctamente', 'loan_id' => $loanId]);
    }

    /**
     * 🔁 Devolver libro
     */
    public function return()
    {
        Session::start();

        if (!\App\Core\Csrf::validate($_POST['_csrf'] ?? null, 'loan_return')) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'msg' => 'Token CSRF inválido (return)']);
            return;
        }

        $loan_id = intval($_POST['loan_id'] ?? 0);
        if (!$loan_id) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'msg' => 'ID de préstamo inválido']);
            return;
        }

        $res = Loan::markReturn($loan_id);
        if (isset($res['error'])) {
            echo json_encode(['ok' => false, 'msg' => $res['error']]);
            return;
        }

        echo json_encode(['ok' => true, 'msg' => 'Libro devuelto correctamente', 'fine' => $res['fine']]);
    }
}
