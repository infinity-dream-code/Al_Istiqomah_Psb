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
        .stop a { color: #1d4ed8; }
    </style>
</head>
<body>
<div class="stop" id="stop">
    <p>Sesi diperbarui. <a href="javascript:history.back()">Kembali</a> · <a href="{{ url('/') }}">Beranda</a></p>
</div>
<script>
(function () {
    var key = 'psbku_419_recover';
    var tries = 0;
    try {
        var saved = JSON.parse(sessionStorage.getItem(key) || 'null');
        if (saved && Date.now() - saved.t < 30000) tries = saved.n || 0;
    } catch (e) {}

    function done(target) {
        try { sessionStorage.setItem(key, JSON.stringify({ n: tries + 1, t: Date.now() })); } catch (e) {}
        location.replace(target);
    }

    if (tries >= 2) {
        try { sessionStorage.removeItem(key); } catch (e) {}
        document.getElementById('stop').style.display = 'block';
        return;
    }

    var path = location.pathname || '/';
    if (/\/ServiceLogout\/?$/i.test(path)) {
        return done(@json(url('/ServiceLogin')));
    }

    var back = document.referrer;
    var sameOrigin = back && back.indexOf(location.origin) === 0;
    var target = sameOrigin ? back : (location.pathname + location.search);

    fetch(@json(url('/session/keepalive')) + '?path=' + encodeURIComponent(sameOrigin ? new URL(back).pathname : path), {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    }).catch(function () {}).finally(function () { done(target); });
})();
</script>
</body>
</html>
