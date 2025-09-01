# Directori del Personal - ICMAB

Una aplicación web progresiva (PWA) para el directorio del personal del Institut de Ciència de Materials de Barcelona.

## 🚀 Características

### Funcionalidades Principales
- **Búsqueda avanzada**: Busca por nombre, apellidos, email y usuario
- **Filtrado por departamento**: Filtra personal por departamento
- **Vistas múltiples**: Vista de grilla y lista
- **Código QR**: Genera QR de la URL actual
- **Diseño responsive**: Optimizado para móviles y tablets

### PWA (Progressive Web App) - Funcionalidad adicional
- **Instalable**: Los usuarios pueden instalar la app en su dispositivo (opcional)
- **Funcionamiento offline**: Cachea recursos para uso sin conexión
- **Experiencia nativa**: Se comporta como una app nativa cuando está instalada
- **Actualizaciones automáticas**: Se actualiza automáticamente
- **No intrusiva**: La instalación es opcional y discreta

## 📱 Instalación como PWA

### En Android (Chrome):
1. Abre la aplicación en Chrome
2. Aparecerá un banner "Añadir a pantalla de inicio"
3. Toca "Añadir" para instalar

### En iOS (Safari):
1. Abre la aplicación en Safari
2. Toca el botón compartir (cuadrado con flecha)
3. Selecciona "Añadir a pantalla de inicio"

### Botón de instalación discreto:
- En dispositivos compatibles aparecerá un pequeño botón circular con icono de descarga en la esquina inferior izquierda
- Es opcional y no interfiere con la experiencia principal de la web

## 🛠️ Instalación del Proyecto

### Requisitos
- Servidor web con PHP 7.4+
- Base de datos MySQL/MariaDB
- HTTPS (requerido para PWA)

### Configuración
1. Clona o descarga los archivos
2. Configura la base de datos en `config.php`
3. Asegúrate de que el servidor tenga HTTPS habilitado
4. Accede a la aplicación

### Archivos principales
- `index.html` - Aplicación principal
- `api_personal.php` - API para datos del personal
- `api_departamentos.php` - API para departamentos
- `manifest.json` - Configuración PWA
- `sw.js` - Service Worker para cacheo offline

## 🔧 Configuración de la Base de Datos

### Estructura requerida:
```sql
-- Tabla de personal
CREATE TABLE icmab_personal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cognom VARCHAR(100),
    cognom2 VARCHAR(100),
    nom VARCHAR(100),
    username VARCHAR(50),
    email VARCHAR(100),
    telefon1 VARCHAR(20),
    despatx VARCHAR(50),
    department_id INT,
    status_id INT DEFAULT 1
);

-- Tabla de departamentos
CREATE TABLE icmab_departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    departament VARCHAR(100)
);
```

## 🌐 Compatibilidad

### Navegadores soportados:
- Chrome 67+
- Firefox 67+
- Safari 11.1+
- Edge 79+

### Funcionalidades PWA:
- **Instalación**: Chrome, Edge, Firefox
- **Offline**: Todos los navegadores modernos
- **Notificaciones**: Chrome, Firefox, Edge

## 📊 Estadísticas

La aplicación muestra:
- Total de personas en el directorio
- Número de resultados filtrados
- Número de departamentos

## 🎨 Personalización

### Colores principales:
- Azul principal: `#0345bf`
- Fondo: `#F5F7F9`
- Texto: `#333`

### Logos:
- Desktop: Banner completo del ICMAB
- Móvil/Tablet: Logo compacto
- PWA: Logo ICMAB como icono

## 🔒 Seguridad

- Solo personal con `status_id=1` es visible
- APIs con validación de entrada
- Headers CORS configurados
- Manejo de errores robusto

## 📈 Rendimiento

- Cacheo inteligente de recursos
- Lazy loading de imágenes
- Optimización para móviles
- Service Worker para cacheo offline

## 🤝 Contribución

Para contribuir al proyecto:
1. Fork el repositorio
2. Crea una rama para tu feature
3. Haz commit de tus cambios
4. Abre un Pull Request

## 📄 Licencia

Este proyecto está desarrollado para el Institut de Ciència de Materials de Barcelona (ICMAB-CSIC).

---

**Desarrollado para ICMAB-CSIC** 🧪
