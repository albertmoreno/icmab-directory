<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Verificar que el archivo de configuración existe
if (!file_exists('config.php')) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Archivo de configuración no encontrado'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once 'config.php';

try {
    $conn = getConnection();

    // Obtener parámetros de búsqueda
    $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
    $departamento = isset($_GET['departamento']) ? $_GET['departamento'] : '';

    // Construir la consulta SQL con JOIN para obtener el nombre del departamento y despacho
    // El departamento y espai_id se obtienen de la última entrada en icmab_dades_laborals
    // El despacho se obtiene de la tabla despatxos usando espai_id
    $sql = "SELECT p.id, p.cognom, p.cognom2, p.nom, p.username, p.email, p.telefon1, p.telefon2, p.bustia, 
                   dl.departament_id, d.departament as department_name,
                   des.descripcio as despatx
            FROM icmab_personal p
            LEFT JOIN (
                SELECT dl1.person_id, dl1.departament_id
                FROM icmab_dades_laborals dl1
                INNER JOIN (
                    SELECT person_id, MAX(id) as max_id
                    FROM icmab_dades_laborals
                    GROUP BY person_id
                ) dl2 ON dl1.person_id = dl2.person_id AND dl1.id = dl2.max_id
            ) dl ON p.id = dl.person_id
            LEFT JOIN icmab_departments d ON dl.departament_id = d.id
            LEFT JOIN icmab_despatxos des ON p.espai_id = des.id
            WHERE p.status_id=1";
    $params = [];

    // Agregar filtros si se proporcionan
    if (!empty($busqueda)) {
        $sql .= " AND (p.cognom LIKE ? OR p.cognom2 LIKE ? OR p.nom LIKE ? OR p.email LIKE ? OR p.username LIKE ?)";
        $busquedaParam = "%$busqueda%";
        $params[] = $busquedaParam;
        $params[] = $busquedaParam;
        $params[] = $busquedaParam;
        $params[] = $busquedaParam;
        $params[] = $busquedaParam;
    }

    if (!empty($departamento)) {
        $sql .= " AND d.departament = ?";
        $params[] = $departamento;
    }

    $sql .= " ORDER BY p.cognom ASC";

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Obtener todos los resultados
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver respuesta JSON
    echo json_encode([
        'success' => true,
        'data' => $personal,
        'total' => count($personal)
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error en la base de datos: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
