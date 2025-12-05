const CACHE_NAME = 'icmab-directori-v4';
const urlsToCache = [
  './',
  './index.html',
  './api_personal.php',
  './api_departamentos.php',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
  'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap',
  'https://unpkg.com/vue@3/dist/vue.global.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'
];

// Instalación del Service Worker
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Cache abierto');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activación del Service Worker
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('Eliminando cache antiguo:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Interceptar peticiones
self.addEventListener('fetch', (event) => {
  // Filtrar peticiones que no pueden ser cacheadas (chrome-extension, chrome:, etc.)
  const url = new URL(event.request.url);
  if (url.protocol === 'chrome-extension:' || 
      url.protocol === 'chrome:' || 
      url.protocol === 'moz-extension:' ||
      url.protocol === 'edge:' ||
      url.protocol === 'opera:') {
    // Dejar que estas peticiones pasen sin intervención del Service Worker
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        // Si está en cache, devolverlo
        if (response) {
          return response;
        }

        // Si no está en cache, hacer la petición a la red
        return fetch(event.request)
          .then((response) => {
            // Verificar que la respuesta es válida
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // Verificar que la URL no sea de extensiones antes de cachear
            const responseUrl = new URL(response.url);
            if (responseUrl.protocol === 'chrome-extension:' || 
                responseUrl.protocol === 'chrome:' || 
                responseUrl.protocol === 'moz-extension:' ||
                responseUrl.protocol === 'edge:' ||
                responseUrl.protocol === 'opera:') {
              return response;
            }

            // Clonar la respuesta para cachearla
            const responseToCache = response.clone();

            caches.open(CACHE_NAME)
              .then((cache) => {
                cache.put(event.request, responseToCache);
              })
              .catch((error) => {
                // Ignorar errores al cachear (puede fallar en algunos casos)
                console.warn('Error al cachear:', error);
              });

            return response;
          })
          .catch(() => {
            // Si no hay conexión y es una petición a la API, devolver datos vacíos
            if (event.request.url.includes('api_personal.php')) {
              return new Response(JSON.stringify({
                success: true,
                data: [],
                total: 0
              }), {
                headers: { 'Content-Type': 'application/json' }
              });
            }
            
            if (event.request.url.includes('api_departamentos.php')) {
              return new Response(JSON.stringify({
                success: true,
                data: []
              }), {
                headers: { 'Content-Type': 'application/json' }
              });
            }
          });
      })
  );
});
