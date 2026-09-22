<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store">
    <title></title>
    <style>
        body { margin: 0; min-height: 100vh; background: #fff; }
    </style>
</head>
<body>
<script>
(function () {
    // JANGAN pernah lempar user ke "/" atau "/ServiceLogin" hanya karena error transient.
    var reloadUrl = @json($reloadUrl ?? '');
    var fallbackUrl = @json($fallbackUrl ?? '');
    var key = 'psbku_soft_recover:' + (location.pathname || '');
    var maxTries = 2;
    var tries = 0;

    try { tries = parseInt(sessionStorage.getItem(key) || '0', 10) || 0; } catch (e) {}

    if (tries < maxTries) {
        try { sessionStorage.setItem(key, String(tries + 1)); } catch (e) {}
        var target = reloadUrl || (location.pathname + location.search);
        var sep = target.indexOf('?') >= 0 ? '&' : '?';
        setTimeout(function () {
            location.replace(target + sep + '_r=' + Date.now());
        }, 350 + tries * 250);
        return;
    }

    try { sessionStorage.removeItem(key); } catch (e) {}

    // Setelah retry: tetap di area app (dashboard), bukan home publik / login
    if (fallbackUrl && fallbackUrl !== '/' && !/ServiceLogin/i.test(fallbackUrl)) {
        location.replace(fallbackUrl);
        return;
    }

    // Last resort: reload halaman yang sama sekali lagi
    location.replace(location.pathname + '?_r=' + Date.now());
})();
</script>
</body>
</html>
