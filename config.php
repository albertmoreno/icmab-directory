<?php
// Configuración de la base de datos
define('DB_HOST', 'ls-fcd38e5539374a6b76db30bc7cbeaeb9cac63d2f.crtzec2dueus.eu-west-3.rds.amazonaws.com');
define('DB_USER', 'dbreaduser');  // Cambiar por tu usuario de MySQL
define('DB_PASS', '$JeeBEETZ14pe');      // Cambiar por tu contraseña de MySQL
define('DB_NAME', 'ICMAB-laravel'); // Nombre de la base de datos

// Crear conexión
function getConnection()
{
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES utf8");
        return $conn;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
