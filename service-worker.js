self.addEventListener("install", (e) => {
    e.waitUntil(
      caches.open("wedding-app").then((cache) => {
        return cache.addAll([
          "/",
          "/index.php",
          "/style.css",
          "/icon-192.png",
          "/icon-512.png",
          // Tambahkan file lain yang penting
        ]);
      })
    );
  });
  
  self.addEventListener("fetch", (e) => {
    e.respondWith(
      caches.match(e.request).then((response) => {
        return response || fetch(e.request);
      })
    );
  });
  