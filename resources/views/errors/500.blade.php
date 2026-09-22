<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, sans-serif;
            background: #f8fafc;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <script>
        (function () {
            var key = 'psbku_500_reload:' + location.pathname;
            var isLogin = /ServiceLogin/i.test(location.pathname);

            try {
                if (!sessionStorage.getItem(key)) {
                    sessionStorage.setItem(key, '1');
                    var qs = location.search ? location.search + '&' : '?';
                    setTimeout(function () {
                        location.replace(location.pathname + qs + '_r=' + Date.now());
                    }, 400);
                    return;
                }
                sessionStorage.removeItem(key);
            } catch (e) {}

            // Setelah 1x reload masih gagal: jangan loop di login, jangan tampilkan pesan error
            if (isLogin) {
                location.replace('/');
                return;
            }
            location.replace('/');
        })();
    </script>
</body>
</html>
