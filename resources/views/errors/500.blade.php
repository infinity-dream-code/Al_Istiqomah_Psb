<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store">
    <title>Memulihkan…</title>
    <style>
        body{margin:0;background:#fff;min-height:100vh;font-family:system-ui,sans-serif;color:#333}
        .stop{display:none;max-width:28rem;margin:15vh auto;padding:1.5rem;text-align:center}
        .stop h1{font-size:1.15rem;margin:0 0 .5rem;font-weight:600}
        .stop p{margin:0 0 1rem;font-size:.9rem;line-height:1.45;color:#555}
        .stop a{color:#1d4ed8}
    </style>
</head>
<body>
<div class="stop" id="stop">
    <h1>Halaman gagal dimuat</h1>
    <p>Error server berulang. Muat ulang manual, atau cek log di server.</p>
    <p><a href="javascript:location.reload()">Muat ulang</a>
        · <a href="{{ url('/ServiceLogin') }}">Ke login</a></p>
</div>
<script>
(function () {
    // Soft recover: reload halaman yang SAMA — jangan ke "/" atau login
    var key = 'psbku_500_reload:' + location.pathname;
    var tries = 0;
    try { tries = parseInt(sessionStorage.getItem(key) || '0', 10) || 0; } catch (e) {}

    if (tries < 2) {
        try { sessionStorage.setItem(key, String(tries + 1)); } catch (e) {}
        var qs = location.search ? location.search.replace(/([?&])_r=\d+/g, '$1').replace(/[?&]$/, '') : '';
        qs = qs && qs !== '?' ? qs + '&_r=' + Date.now() : '?_r=' + Date.now();
        setTimeout(function () {
            location.replace(location.pathname + qs);
        }, 400 + tries * 300);
        return;
    }

    // Habis retry: STOP — jangan redirect lagi (mencegah loop _r)
    try { sessionStorage.removeItem(key); } catch (e) {}
    var el = document.getElementById('stop');
    if (el) el.style.display = 'block';
})();
</script>
</body>
</html>
