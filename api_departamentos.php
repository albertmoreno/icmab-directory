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

    // Obtener departamentos únicos
    $sql = "SELECT DISTINCT departament FROM icmab_departments WHERE departament IS NOT NULL AND departament != '' ORDER BY departament ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $departamentos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Devolver respuesta JSON
    echo json_encode([
        'success' => true,
        'data' => $departamentos
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
