/**
 * Session keep-alive + silent CSRF (419) recovery.
 * Refresh token sebelum submit form supaya 419 jarang muncul.
 */
(function () {
    if (window.__psbkuSessionGuard) return;
    window.__psbkuSessionGuard = true;

    var nativeFetch = window.fetch.bind(window);
    var keepaliveUrl = (document.querySelector('meta[name="session-keepalive"]') || {}).content
        || '/session/keepalive';
    var refreshing = null;
    var navigating = false;

    // Saat klik menu, batalkan keepalive supaya tidak race dengan request halaman baru
    // (file session Windows sering “kosong” kalau dua request nulis bersamaan).
    window.addEventListener('pagehide', function () { navigating = true; });
    window.addEventListener('beforeunload', function () { navigating = true; });

    function setToken(token) {
        if (!token) return token;
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.setAttribute('content', token);
        document.querySelectorAll('input[name="_token"]').forEach(function (el) {
            el.value = token;
        });
        return token;
    }

    function currentToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    function refreshToken() {
        if (navigating) return Promise.resolve(currentToken());
        if (refreshing) return refreshing;
        refreshing = nativeFetch(keepaliveUrl, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function (data) {
                setToken(data.token);
                return data.token;
            })
            .catch(function () { return currentToken(); })
            .finally(function () { refreshing = null; });
        return refreshing;
    }

    window.__psbkuRefreshCsrf = refreshToken;

    // Keep-alive tiap 4 menit + saat tab aktif lagi
    setInterval(refreshToken, 4 * 60 * 1000);
    if (document.readyState === 'complete') {
        refreshToken();
    } else {
        window.addEventListener('load', function () { refreshToken(); });
    }
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') refreshToken();
    });

    // Sebelum submit form POST: refresh token dulu (cegah 419)
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        var method = (form.getAttribute('method') || 'GET').toUpperCase();
        if (method === 'GET') return;
        if (form.dataset.csrfReady === '1') return;

        e.preventDefault();
        e.stopPropagation();

        refreshToken().finally(function () {
            setToken(currentToken());
            form.dataset.csrfReady = '1';
            // Native submit tanpa trigger listener lagi
            HTMLFormElement.prototype.submit.call(form);
        });
    }, true);

    function headersToObject(headers) {
        if (!headers) return {};
        if (typeof Headers !== 'undefined' && headers instanceof Headers) {
            var obj = {};
            headers.forEach(function (v, k) { obj[k] = v; });
            return obj;
        }
        return Object.assign({}, headers);
    }

    function withCsrfHeaders(init, token) {
        init = Object.assign({}, init || {});
        var headers = headersToObject(init.headers);
        if (token) {
            headers['X-CSRF-TOKEN'] = token;
            // Jangan set X-XSRF-TOKEN dengan plain token (harus encrypted cookie)
        }
        headers['X-Requested-With'] = headers['X-Requested-With'] || 'XMLHttpRequest';
        init.headers = headers;

        if (typeof FormData !== 'undefined' && init.body instanceof FormData && token) {
            init.body.set('_token', token);
        } else if (typeof init.body === 'string' && token) {
            if (init.body.indexOf('_token=') !== -1) {
                init.body = init.body.replace(/(^|&)_token=[^&]*/, '$1_token=' + encodeURIComponent(token));
            }
        }
        return init;
    }

    window.fetch = function (input, init) {
        init = init || {};
        if (init._csrfRetried) {
            return nativeFetch(input, init);
        }
        return nativeFetch(input, init).then(function (response) {
            if (response.status !== 419) return response;
            var headerToken = response.headers.get('X-CSRF-TOKEN');
            return refreshToken().then(function (token) {
                var nextInit = withCsrfHeaders(
                    Object.assign({}, init, { _csrfRetried: true }),
                    token || headerToken || currentToken()
                );
                return nativeFetch(input, nextInit);
            });
        });
    };

    var XO = XMLHttpRequest.prototype.open;
    var XS = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.open = function () {
        this.__psbkuMethod = arguments[0];
        this.__psbkuUrl = arguments[1];
        this.__psbkuAsync = arguments[2] !== false;
        return XO.apply(this, arguments);
    };
    XMLHttpRequest.prototype.send = function (body) {
        var xhr = this;
        if (xhr.__psbkuGuard) {
            return XS.call(xhr, body);
        }
        xhr.__psbkuGuard = true;

        var token = currentToken();
        if (token) {
            try { xhr.setRequestHeader('X-CSRF-TOKEN', token); } catch (e) {}
        }

        var originalOnReady = xhr.onreadystatechange;
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 419 && !xhr.__psbkuRetried) {
                xhr.__psbkuRetried = true;
                refreshToken().then(function (newToken) {
                    var retry = new XMLHttpRequest();
                    retry.__psbkuGuard = true;
                    retry.open(xhr.__psbkuMethod, xhr.__psbkuUrl, xhr.__psbkuAsync);
                    if (newToken) {
                        try { retry.setRequestHeader('X-CSRF-TOKEN', newToken); } catch (e) {}
                    }
                    try { retry.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); } catch (e) {}
                    var retryBody = body;
                    if (typeof FormData !== 'undefined' && body instanceof FormData && newToken) {
                        body.set('_token', newToken);
                        retryBody = body;
                    }
                    retry.onreadystatechange = originalOnReady;
                    if (typeof xhr.onload === 'function') retry.onload = xhr.onload.bind(xhr);
                    if (typeof xhr.onerror === 'function') retry.onerror = xhr.onerror.bind(xhr);
                    XS.call(retry, retryBody);
                });
                return;
            }
            if (typeof originalOnReady === 'function') {
                return originalOnReady.apply(xhr, arguments);
            }
        };

        return XS.call(xhr, body);
    };
})();
