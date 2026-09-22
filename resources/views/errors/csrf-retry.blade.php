<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ $token }}">
    <title></title>
</head>
<body>
<form id="csrf-retry-form" method="POST" action="{{ $action }}" style="display:none">
    <input type="hidden" name="_token" value="{{ $token }}">
    <input type="hidden" name="_csrf_retry" value="1">
    @if (!in_array(strtoupper($method), ['GET', 'POST'], true))
        <input type="hidden" name="_method" value="{{ strtoupper($method) }}">
    @endif
    @foreach ($input as $key => $value)
        @include('errors.partials.hidden-input', ['name' => $key, 'value' => $value])
    @endforeach
</form>
<script>
(function () {
    var form = document.getElementById('csrf-retry-form');
    var keepalive = @json(url('/session/keepalive'));

    function submitWith(token) {
        if (token) {
            form.querySelector('input[name="_token"]').value = token;
            var meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', token);
        }
        form.submit();
    }

    fetch(keepalive, {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(function (r) { return r.ok ? r.json() : {}; })
        .then(function (d) { submitWith(d.token || form.querySelector('input[name="_token"]').value); })
        .catch(function () { submitWith(null); });
})();
</script>
</body>
</html>
