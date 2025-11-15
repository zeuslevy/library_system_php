<?php
namespace App\Core;

class Csrf
{
    /**
     * Duración máxima del token (en segundos)
     * 30 minutos = 1800 segundos
     */
    private const TOKEN_LIFETIME = 1800;

    /**
     * Genera o devuelve un token CSRF.
     * Puedes usar un "contexto" opcional (por ejemplo: 'login', 'registro', etc.)
     */
    public static function token(string $context = 'default'): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Limpia tokens antiguos
        self::cleanup();

        // Si no existe un token para este contexto o expiró → genera uno nuevo
        if (
            !isset($_SESSION['_csrf'][$context]['value']) ||
            time() > ($_SESSION['_csrf'][$context]['expires'] ?? 0)
        ) {
            $_SESSION['_csrf'][$context] = [
                'value' => bin2hex(random_bytes(32)),
                'expires' => time() + self::TOKEN_LIFETIME
            ];
        }

        return $_SESSION['_csrf'][$context]['value'];
    }

    /**
     * Alias para compatibilidad (ej. Csrf::generate())
     */
    public static function generate(string $context = 'default'): string
    {
        return self::token($context);
    }

    /**
     * Valida un token CSRF recibido.
     * Si es válido, opcionalmente puede invalidarlo (para tokens de un solo uso).
     */
    public static function validate(?string $token, string $context = 'default', bool $invalidate = false): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (
            empty($_SESSION['_csrf'][$context]['value']) ||
            empty($_SESSION['_csrf'][$context]['expires'])
        ) {
            return false;
        }

        $valid = hash_equals($_SESSION['_csrf'][$context]['value'], (string)$token)
                 && time() <= $_SESSION['_csrf'][$context]['expires'];

        if ($valid && $invalidate) {
            unset($_SESSION['_csrf'][$context]);
        }

        return $valid;
    }

    /**
     * Elimina tokens expirados de la sesión
     */
    private static function cleanup(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        if (!isset($_SESSION['_csrf']) || !is_array($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = [];
            return;
        }

        foreach ($_SESSION['_csrf'] as $ctx => $data) {
            if (!isset($data['expires']) || time() > $data['expires']) {
                unset($_SESSION['_csrf'][$ctx]);
            }
        }
    }
}
