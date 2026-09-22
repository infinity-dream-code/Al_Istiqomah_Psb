<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store">
    <title></title>
    <style>body{margin:0;background:#fff;min-height:100vh}</style>
</head>
<body>
<script>
(function () {
    // Soft recover: reload halaman yang SAMA — jangan ke "/" atau login
    var key = 'psbku_500_reload:' + location.pathname;
    var tries = 0;
    try { tries = parseInt(sessionStorage.getItem(key) || '0', 10) || 0; } catch (e) {}

    if (tries < 2) {
        try { sessionStorage.setItem(key, String(tries + 1)); } catch (e) {}
        var qs = location.search ? location.search + '&' : '?';
        setTimeout(function () {
            location.replace(location.pathname + qs + '_r=' + Date.now());
        }, 400 + tries * 300);
        return;
    }

    try { sessionStorage.removeItem(key); } catch (e) {}

    // Masih gagal: kalau sedang di area pages, tetap di path itu; jangan home/login
    if (/^\/pages\//i.test(location.pathname) || /^\/PmbMstPendaftarans\//i.test(location.pathname)) {
        location.replace(location.pathname + '?_r=' + Date.now());
        return;
    }

    // Non-app path: coba dashboard umum (bukan home publik)
    location.replace('/pages/dashboard?_r=' + Date.now());
})();
</script>
</body>
</html>
