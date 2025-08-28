# Directorio ICMAB - Conexión con MySQL

Esta aplicación web muestra un directorio de personal del ICMAB con datos obtenidos desde una base de datos MySQL local.

## Archivos del proyecto

- `icmab-dir.html` - Aplicación web principal
- `config.php` - Configuración de conexión a la base de datos
- `api_personal.php` - API para obtener datos del personal
- `api_departamentos.php` - API para obtener departamentos
- `database.sql` - Script SQL para crear la base de datos (opcional)

## Configuración

### 1. Configurar la base de datos

Edita el archivo `config.php` y ajusta los parámetros de conexión:

```php
define('DB_HOST', 'localhost');     // Host de MySQL
define('DB_USER', 'root');          // Usuario de MySQL
define('DB_PASS', '');              // Contraseña de MySQL
define('DB_NAME', 'icmab_dir');     // Nombre de tu base de datos
```

### 2. Estructura de la tabla

Asegúrate de que tu tabla `personal` tenga la siguiente estructura:

```sql
CREATE TABLE personal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    despacho VARCHAR(50),
    departamento VARCHAR(100),
    cargo VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 3. Ejecutar la aplicación

1. Coloca todos los archivos en tu servidor web (Apache/Nginx con PHP)
2. Asegúrate de que PHP tenga acceso a MySQL
3. Abre `icmab-dir.html` en tu navegador

## Funcionalidades

- **Búsqueda en tiempo real**: Busca por nombre, email o username
- **Filtro por departamento**: Filtra el personal por departamento
- **Datos dinámicos**: Los datos se cargan desde MySQL
- **Imágenes de perfil**: Muestra fotos de perfil con fallback a avatar genérico
- **Información completa**: Muestra teléfono, email, despacho, departamento y cargo

## APIs disponibles

### GET /api_personal.php
Obtiene la lista del personal con filtros opcionales:

- `busqueda`: Busca en nombre, email o username
- `departamento`: Filtra por departamento específico

Ejemplo: `api_personal.php?busqueda=juan&departamento=Departamento de Física`

### GET /api_departamentos.php
Obtiene la lista de departamentos disponibles.

## Solución de problemas

### Error de conexión a la base de datos
- Verifica que MySQL esté ejecutándose
- Comprueba las credenciales en `config.php`
- Asegúrate de que la base de datos existe

### Error 500 en las APIs
- Verifica que PHP tenga permisos para leer los archivos
- Comprueba los logs de error de PHP
- Asegúrate de que la extensión PDO esté habilitada

### No se cargan las imágenes
- Las imágenes se buscan en `https://media.icmab.es/staff/people/{username}.jpg`
- Si no existe la imagen, se muestra un avatar genérico
