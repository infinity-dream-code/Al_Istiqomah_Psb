/**
 * Session keep-alive + silent CSRF (419) / session hilang (401) recovery.
 * Keepalive mengirim path halaman aktif supaya server bisa membuat ulang session dari cookie cadangan.
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
        var url = keepaliveUrl + (keepaliveUrl.indexOf('?') >= 0 ? '&' : '?')
            + 'path=' + encodeURIComponent(location.pathname + location.search);
        refreshing = nativeFetch(url, {
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

    // Snapshot isian form sebelum submit; dipulihkan kalau server mengembalikan user ke form
    // ini karena gagal simpan (meta form-recover), supaya ketikan user tidak hilang.
    var DRAFT_PREFIX = 'psbku_draft:';
    var DRAFT_TTL = 30 * 60 * 1000;
    var SKIP_FIELD = /^(_token|_method|_csrf_retry|password|password_confirmation|current_password)$/;

    function draftKey(form) {
        var forms = Array.prototype.slice.call(document.forms);
        return DRAFT_PREFIX + location.pathname + location.search + '#' + forms.indexOf(form);
    }

    function saveDraft(form) {
        try {
            var fields = [];
            Array.prototype.forEach.call(form.elements, function (el) {
                if (!el.name || SKIP_FIELD.test(el.name) || el.disabled) return;
                var type = (el.type || '').toLowerCase();
                if (type === 'file' || type === 'password' || type === 'submit' || type === 'button') return;
                if (type === 'checkbox' || type === 'radio') {
                    fields.push({ n: el.name, v: el.value, c: el.checked });
                } else if (el.tagName === 'SELECT' && el.multiple) {
                    fields.push({ n: el.name, m: Array.prototype.filter.call(el.options, function (o) { return o.selected; }).map(function (o) { return o.value; }) });
                } else {
                    fields.push({ n: el.name, v: el.value });
                }
            });
            sessionStorage.setItem(draftKey(form), JSON.stringify({ t: Date.now(), f: fields }));
        } catch (err) {}
    }

    function restoreDrafts() {
        var recover = !!document.querySelector('meta[name="form-recover"]');
        Array.prototype.forEach.call(document.forms, function (form) {
            var key = draftKey(form);
            var raw = null;
            try { raw = sessionStorage.getItem(key); sessionStorage.removeItem(key); } catch (err) {}
            if (!recover || !raw) return;

            var draft;
            try { draft = JSON.parse(raw); } catch (err) { return; }
            if (!draft || !draft.f || Date.now() - draft.t > DRAFT_TTL) return;

            var seen = {};
            draft.f.forEach(function (item) {
                var els = form.querySelectorAll('[name="' + item.n.replace(/"/g, '\\"') + '"]');
                if (!els.length) return;
                var idx = seen[item.n] || 0;
                seen[item.n] = idx + 1;

                if ('c' in item) {
                    Array.prototype.forEach.call(els, function (el) {
                        if (el.value === item.v) el.checked = item.c;
                    });
                    return;
                }
                var el = els.length > 1 ? els[idx] : els[0];
                if (!el) return;
                if (item.m) {
                    Array.prototype.forEach.call(el.options, function (o) { o.selected = item.m.indexOf(o.value) !== -1; });
                } else {
                    el.value = item.v;
                }
                try { el.dispatchEvent(new Event('input', { bubbles: true })); } catch (err) {}
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', restoreDrafts);
    } else {
        restoreDrafts();
    }

    // Sebelum submit form POST: refresh token dulu (cegah 419)
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        var method = (form.getAttribute('method') || 'GET').toUpperCase();
        if (method === 'GET') return;
        if (form.dataset.csrfReady === '1') return;

        if (!/ServiceLog(in|out)/i.test(form.getAttribute('action') || '')) {
            saveDraft(form);
        }

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
            if (response.status !== 419 && response.status !== 401) return response;
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
            if (xhr.readyState === 4 && (xhr.status === 419 || xhr.status === 401) && !xhr.__psbkuRetried) {
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
