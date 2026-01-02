// WBL Management System - Service Worker
// Network-first caching strategy

const CACHE_NAME = 'wbl-cache-v1';
const OFFLINE_URL = '/offline.html';

// Assets to pre-cache during install
const PRECACHE_ASSETS = [
    '/offline.html',
    '/manifest.json'
];

// Install event - pre-cache essential assets
self.addEventListener('install', (event) => {
    console.log('[SW] Installing service worker...');

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Pre-caching offline assets');
                return cache.addAll(PRECACHE_ASSETS);
            })
            .then(() => {
                // Force the waiting service worker to become active
                return self.skipWaiting();
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating service worker...');

    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((name) => name !== CACHE_NAME)
                        .map((name) => {
                            console.log('[SW] Deleting old cache:', name);
                            return caches.delete(name);
                        })
                );
            })
            .then(() => {
                // Take control of all pages immediately
                return self.clients.claim();
            })
    );
});

// Fetch event - network-first strategy
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip requests to different origins
    if (!request.url.startsWith(self.location.origin)) {
        return;
    }

    // Skip requests with authentication (CSRF tokens, etc.)
    if (request.headers.get('X-CSRF-TOKEN') || request.headers.get('X-Requested-With')) {
        return;
    }

    // Handle navigation requests (HTML pages)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .catch(() => {
                    return caches.match(OFFLINE_URL);
                })
        );
        return;
    }

    // For static assets (CSS, JS, images, fonts) - network first, cache fallback
    if (isStaticAsset(request.url)) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Clone the response before caching
                    if (response.ok) {
                        const responseClone = response.clone();
                        caches.open(CACHE_NAME)
                            .then((cache) => {
                                cache.put(request, responseClone);
                            });
                    }
                    return response;
                })
                .catch(() => {
                    return caches.match(request);
                })
        );
        return;
    }

    // For API and dynamic requests - network only with timeout
    event.respondWith(
        fetch(request)
            .catch(() => {
                // Return offline page for failed navigations
                if (request.headers.get('Accept')?.includes('text/html')) {
                    return caches.match(OFFLINE_URL);
                }
                // Return empty response for failed API calls
                return new Response(JSON.stringify({ error: 'Offline' }), {
                    status: 503,
                    headers: { 'Content-Type': 'application/json' }
                });
            })
    );
});

// Helper function to check if URL is a static asset
function isStaticAsset(url) {
    const staticExtensions = [
        '.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico',
        '.woff', '.woff2', '.ttf', '.eot', '.otf'
    ];

    return staticExtensions.some((ext) => url.includes(ext)) ||
           url.includes('/build/') ||
           url.includes('/images/') ||
           url.includes('fonts.googleapis.com') ||
           url.includes('fonts.gstatic.com') ||
           url.includes('fonts.bunny.net');
}

// Handle messages from the main thread
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
