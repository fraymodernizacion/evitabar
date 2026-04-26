const CACHE_NAME = 'pase-evita-v1';
const APP_SHELL = [
  '/public/assets/css/styles.css',
  '/public/assets/js/app.js',
  '/public/assets/icons/favicon.svg',
  '/public/assets/icons/icon.svg',
  '/public/login.php',
];

self.addEventListener('install', (event) => {
  event.waitUntil(caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL)));
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  event.respondWith(
    caches.match(event.request).then((cached) => cached || fetch(event.request))
  );
});
