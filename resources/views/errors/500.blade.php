<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Memuat ulang…</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, sans-serif;
            background: #f8fafc;
            color: #64748b;
        }
    </style>
</head>
<body>
    <p>Memuat ulang…</p>
    <script>
        (function () {
            var key = 'psbku_500_reload:' + location.pathname + location.search;
            try {
                if (!sessionStorage.getItem(key)) {
                    sessionStorage.setItem(key, '1');
                    setTimeout(function () { location.reload(); }, 600);
                    return;
                }
                sessionStorage.removeItem(key);
            } catch (e) {}
            document.querySelector('p').textContent = 'Terjadi gangguan sementara. Silakan muat ulang halaman.';
        })();
    </script>
</body>
</html>
