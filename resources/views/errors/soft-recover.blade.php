<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store">
    <title>Memulihkan…</title>
    <style>
        body { margin: 0; min-height: 100vh; background: #fff; font-family: system-ui, sans-serif; color: #333; }
        .stop { display: none; max-width: 28rem; margin: 15vh auto; padding: 1.5rem; text-align: center; }
        .stop h1 { font-size: 1.15rem; margin: 0 0 .5rem; font-weight: 600; }
        .stop p { margin: 0 0 1rem; font-size: .9rem; line-height: 1.45; color: #555; }
        .stop a { color: #1d4ed8; }
    </style>
</head>
<body>
<div class="stop" id="stop">
    <h1>Halaman gagal dimuat</h1>
    <p>Server mengembalikan error berulang. Coba muat ulang manual, atau cek <code>storage/logs/laravel.log</code> di server.</p>
    <p><a href="javascript:location.reload()">Muat ulang</a>
        · <a href="{{ url('/ServiceLogin') }}">Ke login</a></p>
</div>
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
        // Buang _r lama supaya query tidak menumpuk
        try {
            var u = new URL(target, location.origin);
            u.searchParams.delete('_r');
            u.searchParams.set('_r', String(Date.now()));
            target = u.pathname + u.search;
        } catch (e) {
            var sep = target.indexOf('?') >= 0 ? '&' : '?';
            target = target + sep + '_r=' + Date.now();
        }
        setTimeout(function () {
            location.replace(target);
        }, 350 + tries * 250);
        return;
    }

    // Habis retry: STOP. Jangan redirect lagi (itu yang bikin loop ?_r=&_r=…)
    try { sessionStorage.removeItem(key); } catch (e) {}
    var el = document.getElementById('stop');
    if (el) el.style.display = 'block';
})();
</script>
</body>
</html>
