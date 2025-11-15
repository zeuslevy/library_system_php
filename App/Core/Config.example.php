<?php

namespace App\Core;

class Config
{
    // ⚠️ Copia este archivo como Config.php y coloca tus valores reales
    public static $db_host = 'localhost';
    public static $db_name = 'db_library';
    public static $db_user = 'TU_USUARIO';
    public static $db_pass = 'TU_CONTRASEÑA';

    // Configuración de préstamos
    public static $loan_days = 14;
    public static $fine_per_day = 0.50;
}