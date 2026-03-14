<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#0d6efd">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(function(reg) {
                console.log('Service Worker registered');
            })
            .catch(function(error) {
                console.log("Service Worker registration failed:", error);
            });
    }
</script>
