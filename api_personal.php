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

    // Construir la consulta SQL con JOIN para obtener el nombre del departamento
    $sql = "SELECT p.id, p.cognom, p.cognom2, p.nom, p.username, p.email, p.telefon1, p.despatx, p.department_id, d.departament as department_name
            FROM icmab_personal p
            LEFT JOIN icmab_departments d ON p.department_id = d.id
            WHERE status_id=1";
    $params = [];

    // Agregar filtros si se proporcionan
    if (!empty($busqueda)) {
        $sql .= " AND (cognom LIKE ? OR cognom2 LIKE ? OR nom LIKE ? OR email LIKE ? OR username LIKE ?)";
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

    $sql .= " ORDER BY cognom ASC";

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
