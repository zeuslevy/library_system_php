# library_system_php_fixed
Proyecto corregido listo para desplegar en Apache (PHP 8) + MySQL.

Instrucciones rápidas:
1. Coloca la carpeta `library_system_php_fixed` en tu `htdocs` o donde sirvas proyectos.
2. Asegúrate de que DocumentRoot apunte a .../library_system_php_fixed/public OR usa la URL:
   http://localhost:8080/proyecto_final/library_system_php_fixed/public/
3. Importa `db/schema.sql` en MySQL Workbench.
4. En MySQL crea el usuario 'biblioteca' con contraseña 'clave123' (si no lo hiciste).
5. Ajusta Config.php si necesitas otra contraseña.

Usuario DB por defecto: biblioteca / clave123
Base de datos: db_library
