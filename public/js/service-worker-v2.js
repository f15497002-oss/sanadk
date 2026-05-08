/**
 * SANADK Service Worker v2
 * Progressive Web App support with offline functionality
 */

const CACHE_VERSION = 'sanadk-v2.0';
const CACHE_URLS = [
  '/',
  '/static/css/style.css',
  '/static/css/professional.css',
  '/static/css/mobile.css',
  '/static/js/main.js',
  '/static/js/dashboard.js',
  '/static/img/logo.svg',
  '/static/img/icon-192.png',
  '/static/img/icon-512.png'
];

// Install event - cache resources
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installing...');
  event.waitUntil(
    caches.open(CACHE_VERSION).then((cache) => {
      console.log('[Service Worker] Caching app shell');
      return cache.addAll(CACHE_URLS).catch(err => {
        console.warn('[Service Worker] Cache addAll error:', err);
      });
    })
  );
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activating...');
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_VERSION) {
            console.log('[Service Worker] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }

  // Skip API requests - always use network
  if (url.pathname.startsWith('/api/')) {
    event.respondWith(
      fetch(request)
        .then(response => {
          // Cache successful API responses
          if (response.status === 200) {
            const cache = caches.open(CACHE_VERSION);
            cache.then(c => c.put(request, response.clone()));
          }
          return response;
        })
        .catch(() => {
          // Return cached API response if network fails
          return caches.match(request).then(response => {
            return response || new Response(
              JSON.stringify({ error: 'Offline' }),
              { status: 503, headers: { 'Content-Type': 'application/json' } }
            );
          });
        })
    );
    return;
  }

  // For other requests, try cache first, then network
  event.respondWith(
    caches.match(request).then((response) => {
      if (response) {
        return response;
      }

      return fetch(request)
        .then((response) => {
          // Cache successful responses
          if (response.status === 200 && request.method === 'GET') {
            const cache = caches.open(CACHE_VERSION);
            cache.then(c => c.put(request, response.clone()));
          }
          return response;
        })
        .catch(() => {
          // Return offline page if available
          if (request.destination === 'document') {
            return caches.match('/');
          }
          return new Response('Offline', { status: 503 });
        });
    })
  );
});

// Background sync for offline actions
self.addEventListener('sync', (event) => {
  console.log('[Service Worker] Background sync:', event.tag);
  
  if (event.tag === 'sync-alerts') {
    event.waitUntil(syncAlerts());
  }
});

// Handle push notifications
self.addEventListener('push', (event) => {
  console.log('[Service Worker] Push notification received');
  
  let notificationData = {
    title: 'SANADK',
    body: 'تنبيه جديد',
    icon: '/static/img/icon-192.png',
    badge: '/static/img/icon-192.png',
    tag: 'sanadk-notification',
    requireInteraction: true
  };

  if (event.data) {
    try {
      const data = event.data.json();
      notificationData = { ...notificationData, ...data };
    } catch (e) {
      notificationData.body = event.data.text();
    }
  }

  event.waitUntil(
    self.registration.showNotification(notificationData.title, notificationData)
  );
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
  console.log('[Service Worker] Notification clicked');
  event.notification.close();

  event.waitUntil(
    clients.matchAll({ type: 'window' }).then((clientList) => {
      // Check if app is already open
      for (let i = 0; i < clientList.length; i++) {
        const client = clientList[i];
        if (client.url === '/' && 'focus' in client) {
          return client.focus();
        }
      }
      // Open new window if not already open
      if (clients.openWindow) {
        return clients.openWindow('/');
      }
    })
  );
});

// Sync alerts function
async function syncAlerts() {
  try {
    const response = await fetch('/api/patient/alerts');
    if (response.ok) {
      const data = await response.json();
      console.log('[Service Worker] Synced alerts:', data);
    }
  } catch (error) {
    console.error('[Service Worker] Sync error:', error);
  }
}

// Message handling
self.addEventListener('message', (event) => {
  console.log('[Service Worker] Message received:', event.data);
  
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

console.log('[Service Worker] Loaded successfully');
