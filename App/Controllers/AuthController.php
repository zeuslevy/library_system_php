<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Session;
use App\Core\Csrf;
use App\Core\Database;

class AuthController
{
    /**
     * Muestra el formulario de inicio de sesión o procesa el login.
     */
    public function login()
    {
        Session::start();
        $base = '/proyecto_final/library_system_php_fixed/public';

        // 📄 Si la solicitud es GET → mostrar formulario
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $view = 'auth/login';
            require __DIR__ . '/../../views/layout.php';
            return;
        }

        // 🚨 Si es POST → procesar login
        if (isset($_POST['_csrf']) && !Csrf::validate($_POST['_csrf'], 'login')) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'msg' => 'Token CSRF inválido']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $msg = 'Por favor, completa todos los campos.';
            if ($this->isAjax()) {
                echo json_encode(['ok' => false, 'msg' => $msg]);
            } else {
                $_SESSION['login_error'] = $msg;
                header("Location: $base/auth/login");
            }
            return;
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $msg = 'Correo o contraseña incorrectos.';
            if ($this->isAjax()) {
                echo json_encode(['ok' => false, 'msg' => $msg]);
            } else {
                $_SESSION['login_error'] = $msg;
                header("Location: $base/auth/login");
            }
            return;
        }

        // 🔐 Iniciar sesión segura
        session_regenerate_id(true);
        Session::set('user', [
            'id'   => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ]);

        // 🔁 Redirigir o responder según el tipo de solicitud
        if ($this->isAjax()) {
            echo json_encode([
                'ok' => true,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            if (in_array($user['role'], ['admin', 'librarian'])) {
                header("Location: $base/dashboard");
            } else {
                header("Location: $base/");
            }
        }
    }

    /**
     * Muestra el formulario de registro o procesa el registro.
     */
    public function register()
    {
        Session::start();
        $base = '/proyecto_final/library_system_php_fixed/public';

        // 📄 Si la solicitud es GET → mostrar formulario
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $view = 'auth/register';
            require __DIR__ . '/../../views/layout.php';
            return;
        }

        // 🚨 Validación de CSRF
        if (isset($_POST['_csrf']) && !Csrf::validate($_POST['_csrf'],'register')) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'msg' => 'Token CSRF inválido']);
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'student'; 

        if ($name === '' || $email === '' || $password === '') {
            echo json_encode(['ok' => false, 'msg' => 'Todos los campos son obligatorios.']);
            return;
        }

        // Comprobar si el correo ya existe
        if (User::findByEmail($email)) {
            echo json_encode(['ok' => false, 'msg' => 'El correo ya está registrado.']);
            return;
        }

        // 🔐 Guardar usuario
        if (!User::create($name, $email, $password, $role)) {
        echo json_encode(['ok' => false, 'msg' => 'El correo ya existe o ocurrió un error al registrar.']);
        return;
        }

        echo json_encode(['ok' => true, 'msg' => 'Usuario registrado correctamente.']);
    }

    /**
     * Cierra sesión y redirige al catálogo.
     */
    public function logout()
    {
        Session::start();
        Session::destroy();
        header("Location: /proyecto_final/library_system_php_fixed/public/");
        exit;
    }

    /**
     * Verifica si la solicitud es AJAX.
     */
    private function isAjax(): bool
    {
        return (
            isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );
    }
}

