const CACHE_NAME = "merotable-v1";

const urlsToCache = [
    "/",
    "/offline",
];

self.addEventListener("install", event => {

    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {

            return Promise.all(
                urlsToCache.map(url => {
                    return cache.add(url).catch(() => {
                        console.log("Failed to cache:", url);
                    });
                })
            );

        })
    );

});

self.addEventListener("fetch", event => {

    if (event.request.method !== "GET") return;

    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );

});