<?php
// Configuración de la base de datos AWS ORIGINAL
/*define('DB_HOST', 'ls-fcd38e5539374a6b76db30bc7cbeaeb9cac63d2f.crtzec2dueus.eu-west-3.rds.amazonaws.com');
define('DB_USER', 'dbmasteruser');
define('DB_PASS', 'v3moy=$.C>AqE`x~uK|QdY|^2`87o:');
define('DB_NAME', 'ICMAB-laravel');*/

// Configuración de la base de datos AWS ORIGINAL SOLO LECTURA
/*define('DB_HOST', 'ls-fcd38e5539374a6b76db30bc7cbeaeb9cac63d2f.crtzec2dueus.eu-west-3.rds.amazonaws.com');
define('DB_NAME', 'ICMAB-laravel');
define('DB_USER', 'dbreaduser');
define('DB_PASS', '$JeeBEETZ14pe');      
*/

// Configuración de la base de datos LOCAL
define('DB_HOST', 'localhost');
define('DB_NAME', 'uriticv2');
define('DB_USER', 'albert');
define('DB_PASS', 'albert');



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
