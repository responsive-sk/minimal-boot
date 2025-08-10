// Service Worker for Minimal Boot - Advanced Caching Strategy
const CACHE_NAME = 'minimal-boot-v1.0.0';
const STATIC_CACHE = 'static-v1.0.0';
const DYNAMIC_CACHE = 'dynamic-v1.0.0';

// Assets to cache immediately
const STATIC_ASSETS = [
  '/',
  '/themes/svelte/assets/main.css',
  '/themes/svelte/assets/main.js',
  '/images/app/logo.svg',
  '/about',
  '/contact'
];

// Install event - cache static assets
self.addEventListener('install', event => {
  console.log('Service Worker: Installing...');
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        console.log('Service Worker: Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => {
        console.log('Service Worker: Static assets cached');
        return self.skipWaiting();
      })
      .catch(err => console.log('Service Worker: Cache failed', err))
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  console.log('Service Worker: Activating...');
  event.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
              console.log('Service Worker: Deleting old cache', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('Service Worker: Activated');
        return self.clients.claim();
      })
  );
});

// Fetch event - serve from cache with network fallback
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests
  if (request.method !== 'GET') return;

  // Skip external requests
  if (url.origin !== location.origin) return;

  // Cache strategy based on request type
  if (isStaticAsset(request.url)) {
    // Static assets: Cache First strategy
    event.respondWith(cacheFirst(request));
  } else if (isAPIRequest(request.url)) {
    // API requests: Network First strategy
    event.respondWith(networkFirst(request));
  } else {
    // HTML pages: Stale While Revalidate strategy
    event.respondWith(staleWhileRevalidate(request));
  }
});

// Cache First strategy for static assets
async function cacheFirst(request) {
  try {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }

    const networkResponse = await fetch(request);
    if (networkResponse.ok) {
      const cache = await caches.open(STATIC_CACHE);
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  } catch (error) {
    console.log('Cache First failed:', error);
    return new Response('Offline', { status: 503 });
  }
}

// Network First strategy for API requests
async function networkFirst(request) {
  try {
    const networkResponse = await fetch(request);
    if (networkResponse.ok) {
      const cache = await caches.open(DYNAMIC_CACHE);
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  } catch (error) {
    const cachedResponse = await caches.match(request);
    return cachedResponse || new Response('Offline', { status: 503 });
  }
}

// Stale While Revalidate strategy for HTML pages
async function staleWhileRevalidate(request) {
  const cache = await caches.open(DYNAMIC_CACHE);
  const cachedResponse = await cache.match(request);

  const fetchPromise = fetch(request).then(networkResponse => {
    if (networkResponse.ok) {
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  }).catch(() => cachedResponse);

  return cachedResponse || fetchPromise;
}

// Helper functions
function isStaticAsset(url) {
  return url.includes('/themes/') || 
         url.includes('/images/') || 
         url.includes('.css') || 
         url.includes('.js') || 
         url.includes('.svg') || 
         url.includes('.png') || 
         url.includes('.jpg') || 
         url.includes('.webp');
}

function isAPIRequest(url) {
  return url.includes('/api/') || url.includes('/ajax/');
}

// Background sync for offline actions
self.addEventListener('sync', event => {
  if (event.tag === 'background-sync') {
    event.waitUntil(doBackgroundSync());
  }
});

async function doBackgroundSync() {
  // Handle offline form submissions, etc.
  console.log('Service Worker: Background sync');
}

// Push notifications (if needed)
self.addEventListener('push', event => {
  if (event.data) {
    const data = event.data.json();
    const options = {
      body: data.body,
      icon: '/images/app/logo.svg',
      badge: '/images/app/logo.svg',
      vibrate: [100, 50, 100],
      data: data
    };
    event.waitUntil(
      self.registration.showNotification(data.title, options)
    );
  }
});
