<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    /**
     * Busca un usuario por su correo electrónico
     */
    public static function findByEmail(string $email): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Obtiene un usuario por su ID
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Crea un nuevo usuario en la base de datos
     */
        
    public static function create(string $name, string $email, string $password, string $role = 'student'): bool 
    {
        $db = Database::getInstance();

        try {
            // 🔎 Verificar si el correo ya está registrado
            $check = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $check->execute([':email' => $email]);

            if ($check->fetchColumn() > 0) {
                return false; // correo ya existe
            }

            // 🔐 Encriptar contraseña con algoritmo por defecto
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // 🧾 Insertar nuevo usuario
            $stmt = $db->prepare("
                INSERT INTO users (name, email, password_hash, role, created_at)
                VALUES (:name, :email, :password_hash, :role, NOW())
            ");

            $ok = $stmt->execute([
                ':name'          => $name,
                ':email'         => $email,
                ':password_hash' => $hash,
                ':role'          => $role
            ]);

            return $ok;

        } catch (\PDOException $e) {
            error_log('Error al crear usuario: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Devuelve todos los usuarios (solo para administradores)
     */
    public static function all(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina un usuario (por ID)
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
