<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Prueba de conexión a la base de datos</h2>";

// Verificar si PHP tiene PDO habilitado
if (!extension_loaded('pdo_mysql')) {
    echo "<p style='color: red;'>❌ Error: La extensión PDO MySQL no está habilitada</p>";
    echo "<p>Para habilitarla, descomenta la línea 'extension=pdo_mysql' en tu php.ini</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ PDO MySQL está habilitado</p>";
}

// Incluir configuración
require_once 'config.php';

try {
    echo "<p>Intentando conectar a la base de datos...</p>";
    echo "<p>Host: " . DB_HOST . "</p>";
    echo "<p>Base de datos: " . DB_NAME . "</p>";
    echo "<p>Usuario: " . DB_USER . "</p>";

    $conn = getConnection();
    echo "<p style='color: green;'>✅ Conexión exitosa a la base de datos</p>";

    // Verificar si la tabla personal existe
    $stmt = $conn->query("SHOW TABLES LIKE 'icmab_personal'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ La tabla 'personal' existe</p>";

        // Contar registros
        $stmt = $conn->query("SELECT COUNT(*) as total FROM icmab_personal");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>📊 Total de registros en la tabla: " . $result['total'] . "</p>";

        // Mostrar algunos registros de ejemplo con nombre de departamento
        $stmt = $conn->query("SELECT p.id, p.username, p.cognom, p.email, p.department_id, d.departament as department_name 
                              FROM icmab_personal p 
                              LEFT JOIN icmab_departments d ON p.department_id = d.id 
                              LIMIT 3");
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($registros) > 0) {
            echo "<h3>Primeros 3 registros:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Username</th><th>Nombre</th><th>Email</th><th>Departamento ID</th><th>Departamento Nombre</th></tr>";
            foreach ($registros as $registro) {
                echo "<tr>";
                echo "<td>" . $registro['id'] . "</td>";
                echo "<td>" . $registro['username'] . "</td>";
                echo "<td>" . $registro['cognom'] . "</td>";
                echo "<td>" . $registro['email'] . "</td>";
                echo "<td>" . $registro['department_id'] . "</td>";
                echo "<td>" . ($registro['department_name'] ?? 'Sin departamento') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ La tabla 'personal' no existe</p>";
        echo "<p>Ejecuta el script database.sql para crear la tabla</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    echo "<h3>Posibles soluciones:</h3>";
    echo "<ul>";
    echo "<li>Verifica que MySQL esté ejecutándose</li>";
    echo "<li>Comprueba las credenciales en config.php</li>";
    echo "<li>Asegúrate de que la base de datos existe</li>";
    echo "<li>Verifica que el usuario tenga permisos</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Información del servidor:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
