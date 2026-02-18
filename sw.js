const CACHE_NAME = 'icmab-directori-v5';
const CACHE_MAX_AGE = 24 * 60 * 60 * 1000; // 24 horas en milisegundos

// Solo recursos estáticos externos (versionados en URL). NO cachear APIs ni HTML en instalación
const urlsToCache = [
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
  'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap',
  'https://unpkg.com/vue@3/dist/vue.global.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'
];

// Instalación del Service Worker - skipWaiting para activar inmediatamente
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(urlsToCache))
      .catch((err) => console.warn('Error precacheando:', err))
  );
});

// Activación - claim para controlar página inmediatamente
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Comprobar si una entrada de cache ha expirado (24h)
async function isCacheExpired(request) {
  try {
    const cache = await caches.open(CACHE_NAME);
    const cachedResponse = await cache.match(request);
    if (!cachedResponse) return true;
    const cachedTime = cachedResponse.headers.get('sw-cached-time');
    if (!cachedTime) return false; // Sin timestamp, considerar válido
    return (Date.now() - parseInt(cachedTime, 10)) > CACHE_MAX_AGE;
  } catch {
    return true;
  }
}

// Añadir timestamp a la respuesta para control de expiración
function addCacheTime(response) {
  const headers = new Headers(response.headers);
  headers.set('sw-cached-time', Date.now().toString());
  return new Response(response.body, {
    status: response.status,
    statusText: response.statusText,
    headers: headers
  });
}

// Es petición a API (datos dinámicos)
function isApiRequest(url) {
  return url.includes('api_personal.php') || url.includes('api_departamentos.php');
}

// Es HTML (página principal)
function isHtmlRequest(url) {
  const path = new URL(url).pathname;
  return path === '/' || path.endsWith('index.html') || path === '';
}

// Filtrar protocolos que no podemos cachear
function shouldSkip(url) {
  const protocol = url.protocol;
  return ['chrome-extension:', 'chrome:', 'moz-extension:', 'edge:', 'opera:'].includes(protocol);
}

// Interceptar peticiones
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);
  if (shouldSkip(url)) return;

  // APIs: SIEMPRE network-first (nunca cache, datos siempre frescos)
  if (isApiRequest(event.request.url)) {
    event.respondWith(
      fetch(event.request)
        .then((response) => response)
        .catch(() => {
          if (event.request.url.includes('api_personal.php')) {
            return new Response(JSON.stringify({ success: true, data: [], total: 0 }), {
              headers: { 'Content-Type': 'application/json' }
            });
          }
          if (event.request.url.includes('api_departamentos.php')) {
            return new Response(JSON.stringify({ success: true, data: [] }), {
              headers: { 'Content-Type': 'application/json' }
            });
          }
          throw new Error('Offline');
        })
    );
    return;
  }

  // HTML (index, /): network-first, cache solo si offline
  if (isHtmlRequest(event.request.url) || event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          if (response && response.status === 200 && response.type === 'basic') {
            const toCache = addCacheTime(response.clone());
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, toCache)).catch(() => {});
          }
          return response;
        })
        .catch(() => caches.match(event.request))
    );
    return;
  }

  // style.css y recursos locales: network-first con cache fallback
  if (url.origin === self.location.origin) {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          if (response && response.status === 200 && response.type === 'basic') {
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, addCacheTime(response.clone()))).catch(() => {});
          }
          return response;
        })
        .catch(() => caches.match(event.request))
    );
    return;
  }

  // CDN/recursos externos: stale-while-revalidate con expiración 24h
  event.respondWith(
    caches.match(event.request).then(async (cachedResponse) => {
      const expired = await isCacheExpired(event.request);
      const fetchPromise = fetch(event.request)
        .then((response) => {
          if (response && response.status === 200 && response.type === 'basic') {
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, addCacheTime(response.clone()))).catch(() => {});
          }
          return response;
        })
        .catch(() => null);

      // Si hay cache válida (< 24h), servirla mientras revalida en background
      if (cachedResponse && !expired) {
        fetchPromise.catch(() => {}); // Revalida en background, ignorar errores
        return cachedResponse;
      }
      // Si no hay cache o expiró, esperar a la red
      const networkResponse = await fetchPromise;
      return networkResponse || cachedResponse;
    })
  );
});
