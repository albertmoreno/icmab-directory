<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Prueba de la tabla de departamentos</h2>";

require_once 'config.php';

try {
    $conn = getConnection();

    // Verificar si la tabla de departamentos existe
    $stmt = $conn->query("SHOW TABLES LIKE 'icmab_departments'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ La tabla 'icmab_departments' existe</p>";

        // Contar registros
        $stmt = $conn->query("SELECT COUNT(*) as total FROM icmab_departments");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>📊 Total de departamentos: " . $result['total'] . "</p>";

        // Mostrar todos los departamentos
        $stmt = $conn->query("SELECT id, departament FROM icmab_departments ORDER BY departament ASC");
        $departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($departamentos) > 0) {
            echo "<h3>Lista de departamentos:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Nombre del Departamento</th></tr>";
            foreach ($departamentos as $depto) {
                echo "<tr>";
                echo "<td>" . $depto['id'] . "</td>";
                echo "<td>" . $depto['departament'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // Verificar la relación entre personal y departamentos
        echo "<h3>Verificación de relaciones:</h3>";
        $stmt = $conn->query("SELECT COUNT(*) as total FROM icmab_personal WHERE department_id IS NOT NULL");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Personas con departamento asignado: " . $result['total'] . "</p>";

        $stmt = $conn->query("SELECT COUNT(*) as total FROM icmab_personal WHERE department_id IS NULL");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Personas sin departamento asignado: " . $result['total'] . "</p>";

        // Mostrar algunos ejemplos de personal con departamento
        $stmt = $conn->query("SELECT p.cognom, p.department_id, d.departament 
                              FROM icmab_personal p 
                              LEFT JOIN icmab_departments d ON p.department_id = d.id 
                              WHERE p.department_id IS NOT NULL 
                              LIMIT 5");
        $ejemplos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($ejemplos) > 0) {
            echo "<h3>Ejemplos de personal con departamento:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Nombre</th><th>Department ID</th><th>Departamento</th></tr>";
            foreach ($ejemplos as $ejemplo) {
                echo "<tr>";
                echo "<td>" . $ejemplo['cognom'] . "</td>";
                echo "<td>" . $ejemplo['department_id'] . "</td>";
                echo "<td>" . $ejemplo['departament'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ La tabla 'icmab_departments' no existe</p>";
        echo "<p>Necesitas crear la tabla de departamentos o verificar el nombre correcto.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . $e->getMessage() . "</p>";
}
