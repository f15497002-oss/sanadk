// SANADK Service Worker
const CACHE_NAME = 'sanadk-v1';
const urlsToCache = [
  '/',
  '/static/css/style.css',
  '/static/css/dashboard.css',
  '/static/js/main.js',
  '/static/js/dashboard.js',
  '/static/js/doctor-dashboard.js',
  '/static/img/logo.svg',
  '/manifest.json'
];

// Install event - cache resources
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('SANADK: Cache opened');
        return cache.addAll(urlsToCache);
      })
      .catch(err => console.log('SANADK: Cache error', err))
  );
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('SANADK: Deleting old cache', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Skip API requests - always fetch from network
  if (event.request.url.includes('/api/')) {
    event.respondWith(
      fetch(event.request)
        .catch(() => {
          // Return offline response for API calls
          return new Response(
            JSON.stringify({
              error: 'Offline',
              message: 'لا يمكن الاتصال بالخادم. يرجى التحقق من الاتصال بالإنترنت.'
            }),
            {
              status: 503,
              statusText: 'Service Unavailable',
              headers: new Headers({
                'Content-Type': 'application/json'
              })
            }
          );
        })
    );
    return;
  }

  // For other requests, use cache-first strategy
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Return cached response if available
        if (response) {
          return response;
        }

        // Otherwise, fetch from network
        return fetch(event.request)
          .then(response => {
            // Don't cache non-successful responses
            if (!response || response.status !== 200 || response.type === 'error') {
              return response;
            }

            // Clone the response
            const responseToCache = response.clone();

            // Cache the response for future use
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
              });

            return response;
          })
          .catch(() => {
            // Return offline page if available
            return caches.match('/offline.html')
              .then(response => {
                return response || new Response(
                  'صفحة غير متاحة في الوضع غير المتصل',
                  {
                    status: 404,
                    statusText: 'Not Found',
                    headers: new Headers({
                      'Content-Type': 'text/plain; charset=utf-8'
                    })
                  }
                );
              });
          });
      })
  );
});

// Background Sync for notifications
self.addEventListener('sync', event => {
  if (event.tag === 'sync-notifications') {
    event.waitUntil(syncNotifications());
  }
});

async function syncNotifications() {
  try {
    const response = await fetch('/api/notifications/pending');
    const data = await response.json();
    
    if (data.notifications && data.notifications.length > 0) {
      data.notifications.forEach(notification => {
        self.registration.showNotification(notification.title, {
          body: notification.body,
          icon: '/static/img/icon-192.png',
          badge: '/static/img/icon-96.png',
          tag: notification.id,
          requireInteraction: notification.severity === 'high'
        });
      });
    }
  } catch (error) {
    console.log('SANADK: Sync error', error);
  }
}

// Push notifications
self.addEventListener('push', event => {
  if (!event.data) {
    return;
  }

  const data = event.data.json();
  const options = {
    body: data.body || 'إشعار جديد من SANADK',
    icon: '/static/img/icon-192.png',
    badge: '/static/img/icon-96.png',
    tag: data.tag || 'notification',
    requireInteraction: data.requireInteraction || false,
    actions: data.actions || []
  };

  event.waitUntil(
    self.registration.showNotification(data.title || 'SANADK', options)
  );
});

// Notification click
self.addEventListener('notificationclick', event => {
  event.notification.close();

  event.waitUntil(
    clients.matchAll({ type: 'window' })
      .then(clientList => {
        // Check if there's already a window open
        for (let i = 0; i < clientList.length; i++) {
          const client = clientList[i];
          if (client.url === '/' && 'focus' in client) {
            return client.focus();
          }
        }
        // If not, open a new window
        if (clients.openWindow) {
          return clients.openWindow(event.notification.data.url || '/');
        }
      })
  );
});

// Message handling
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

console.log('SANADK Service Worker loaded successfully');
