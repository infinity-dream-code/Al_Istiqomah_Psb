<meta name="session-keepalive" content="{{ url('/session/keepalive') }}">
@if (!isset($__psbkuSessionGuardLoaded))
    @php($__psbkuSessionGuardLoaded = true)
    <script src="{{ asset('js/session-guard.js') }}" defer></script>
@endif
