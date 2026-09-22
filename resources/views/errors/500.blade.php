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
    <p id="msg"></p>
    <script>
        (function () {
            var path = location.pathname + location.search;
            var key = 'psbku_500_reload:' + location.pathname;
            var isLogin = /ServiceLogin/i.test(location.pathname);

            try {
                if (!sessionStorage.getItem(key)) {
                    sessionStorage.setItem(key, '1');
                    // Cache-bust reload sekali
                    var url = location.pathname + (location.search ? location.search + '&' : '?') + '_r=' + Date.now();
                    setTimeout(function () { location.replace(url); }, 400);
                    return;
                }
                sessionStorage.removeItem(key);
            } catch (e) {}

            // Jangan tampilkan pesan error — redirect diam-diam
            if (isLogin) {
                location.replace('/ServiceLogin');
                return;
            }
            location.replace('/');
        })();
    </script>
</body>
</html>
