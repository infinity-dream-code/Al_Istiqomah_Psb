<meta name="session-keepalive" content="{{ url('/session/keepalive') }}">
@if (session('recover_error'))
<meta name="form-recover" content="1">
<div id="psbku-recover-toast" role="alert"
     style="position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:99999;max-width:92vw;width:520px;background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 40px 12px 14px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);font:500 14px/1.45 system-ui,sans-serif;">
    {{ session('recover_error') }}
    <button type="button" onclick="this.parentNode.remove()" aria-label="Tutup"
            style="position:absolute;top:8px;right:10px;background:none;border:0;color:#b91c1c;font-size:18px;cursor:pointer;">&times;</button>
</div>
<script>setTimeout(function(){var t=document.getElementById('psbku-recover-toast');if(t)t.remove();},12000);</script>
@endif
<script src="{{ asset('js/session-guard.js') }}" defer></script>
