<?php
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Prueba de las APIs</h2>";

// Función para hacer peticiones HTTP
function testAPI($url, $description) {
    echo "<h3>$description</h3>";
    echo "<p>URL: $url</p>";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        echo "<p style='color: red;'>❌ Error al acceder a la API</p>";
        echo "<p>Verifica que el servidor web esté ejecutándose</p>";
        return false;
    }
    
    $data = json_decode($response, true);
    
    if ($data === null) {
        echo "<p style='color: red;'>❌ Error al decodificar JSON</p>";
        echo "<p>Respuesta recibida: " . htmlspecialchars($response) . "</p>";
        return false;
    }
    
    if (isset($data['success']) && $data['success']) {
        echo "<p style='color: green;'>✅ API funcionando correctamente</p>";
        if (isset($data['total'])) {
            echo "<p>Total de registros: " . $data['total'] . "</p>";
        }
        if (isset($data['data']) && is_array($data['data'])) {
            echo "<p>Número de elementos: " . count($data['data']) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error en la API</p>";
        if (isset($data['error'])) {
            echo "<p>Error: " . $data['error'] . "</p>";
        }
    }
    
    echo "<hr>";
    return true;
}

// Obtener la URL base
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['REQUEST_URI']);
$baseUrl = "$protocol://$host$path";

echo "<p><strong>URL base:</strong> $baseUrl</p>";

// Probar API de departamentos
testAPI("$baseUrl/api_departamentos.php", "API de Departamentos");

// Probar API de personal
testAPI("$baseUrl/api_personal.php", "API de Personal (sin filtros)");

// Probar API de personal con búsqueda
testAPI("$baseUrl/api_personal.php?busqueda=juan", "API de Personal (con búsqueda 'juan')");

echo "<h3>Prueba manual de las APIs</h3>";
echo "<p>Puedes probar manualmente las siguientes URLs:</p>";
echo "<ul>";
echo "<li><a href='api_departamentos.php' target='_blank'>api_departamentos.php</a></li>";
echo "<li><a href='api_personal.php' target='_blank'>api_personal.php</a></li>";
echo "<li><a href='api_personal.php?busqueda=juan' target='_blank'>api_personal.php?busqueda=juan</a></li>";
echo "</ul>";

echo "<h3>Verificación de archivos</h3>";
$files = ['config.php', 'api_personal.php', 'api_departamentos.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $file no existe</p>";
    }
}
?>
