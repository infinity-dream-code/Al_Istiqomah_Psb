<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>PSB DEMO</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    @keyframes slideInLeft {
      from { opacity: 0; transform: translateX(-50px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(50px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes floatSmooth {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-15px); }
    }

    @keyframes gradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .animate-slideInLeft { animation: slideInLeft 0.6s ease-out forwards; }
    .animate-slideInRight { animation: slideInRight 0.6s ease-out forwards; }
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
    .animate-floatSmooth { animation: floatSmooth 4s ease-in-out infinite; }

    .gradient-bg {
      background: linear-gradient(-45deg, #059669, #047857, #10b981, #065f46);
      background-size: 400% 400%;
      animation: gradientMove 10s ease infinite;
    }

    .input-focus { transition: all 0.3s ease; }
    .input-focus:focus {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
    }

    .btn-hover { transition: all 0.3s ease; }
    .btn-hover:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
    }
    .btn-hover:active { transform: translateY(-1px); }

    .icon-link { transition: all 0.3s ease; }
    .icon-link:hover { transform: translateY(-4px) scale(1.05); }
    .icon-link:active { transform: translateY(-2px) scale(1.02); }

    @media (max-width: 1024px) {
      .main-container { flex-direction: column; max-width: 100%; }
      .left-panel { width: 100% !important; min-height: 250px; padding: 2rem !important; }
      .right-panel { width: 100% !important; padding: 2rem !important; }
      .logo-img { max-height: 200px !important; }
    }

    @media (max-width: 640px) {
      .main-container { margin: 1rem; border-radius: 1rem; }
      .left-panel { min-height: 200px; padding: 1.5rem !important; }
      .right-panel { padding: 1.5rem !important; }
      .form-title { font-size: 1.75rem !important; }
      .logo-img { max-height: 150px !important; }
      .icon-link { width: 2.5rem !important; height: 2.5rem !important; }
      .icon-link svg { width: 1.25rem !important; height: 1.25rem !important; }
    }

    @media (max-width: 400px) {
      .main-container { margin: 0.5rem; }
      .right-panel { padding: 1rem !important; }
      .input-field { padding: 0.625rem !important; font-size: 0.875rem; }
      .submit-btn { padding: 0.625rem 1rem !important; font-size: 1rem !important; }
    }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-6xl bg-white shadow-2xl rounded-2xl flex overflow-hidden main-container">

    <div class="w-1/2 gradient-bg flex items-center justify-center p-12 relative overflow-hidden left-panel">
      <div class="animate-slideInLeft">
        <img src="https://demo.pmb.smartpayment.co.id/images/bg-logo.png"
             alt="PSB Demo Logo"
             class="max-w-full max-h-96 object-contain animate-floatSmooth logo-img"
             onerror="this.style.display='none';">
      </div>
    </div>

    <div class="w-1/2 p-8 lg:p-12 flex flex-col justify-center bg-white right-panel">
      <div class="max-w-md mx-auto w-full animate-slideInRight">

        @if (session('login_error'))
          <div class="flex items-center gap-3 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('login_error') }}</span>
          </div>
        @endif

        @if (session('success'))
          <div class="flex items-center gap-3 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
          </div>
        @endif

        @if (session('need_payment'))
          <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-300 text-yellow-700 px-4 py-3 rounded-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
              <p class="text-sm font-medium">Akun Anda belum melakukan pembayaran pendaftaran.</p>
              <a href="{{ session('redirect_link') }}" class="text-sm underline font-semibold mt-1 inline-block">Klik di sini untuk melakukan pembayaran →</a>
            </div>
          </div>
        @endif

        <h1 class="text-3xl lg:text-4xl font-bold text-center text-gray-800 mb-2 form-title">DEMO PSB</h1>
        <p class="text-center text-gray-400 mb-8 text-sm font-medium tracking-wider">FORM LOGIN</p>

        <form class="space-y-6 animate-fadeIn" method="POST" action="{{ route('login') }}">
          @csrf
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
            <input
              type="text"
              name="username"
              required
              class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:border-green-600 input-focus input-field">
          </div>

          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input
              type="password"
              name="password"
              id="password"
              required
              class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:border-green-600 input-focus input-field">
            <button
              type="button"
              id="togglePassword"
              class="absolute right-3 top-11 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors">
              <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
              </svg>
            </button>
          </div>

          <button
            type="submit"
            id="loginSubmitBtn"
            class="w-full bg-green-700 text-white py-3 px-6 rounded-lg font-medium text-lg shadow-lg btn-hover submit-btn">
            Log In
          </button>
        </form>

        <div class="mt-8 text-center">
          <p class="text-gray-400 text-sm mb-6">— or Another Links —</p>
          <div class="flex justify-center gap-4">
            <a href="{{url('/')}}" class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-600 text-white shadow-lg icon-link">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
              </svg>
            </a>
            <a href="{{url('/enroll')}}" class="w-12 h-12 flex items-center justify-center rounded-full bg-sky-400 text-white shadow-lg icon-link">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
              </svg>
            </a>
            <a href="#" class="w-12 h-12 flex items-center justify-center rounded-full bg-red-500 text-white shadow-lg icon-link">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
              </svg>
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const passwordInput = document.getElementById('password');
      const togglePassword = document.getElementById('togglePassword');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');
      const loginForm = document.querySelector('form[action]');
      const loginBtn = document.getElementById('loginSubmitBtn');

      if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function() {
          loginBtn.disabled = true;
          loginBtn.textContent = 'Memproses...';
          loginBtn.classList.add('opacity-70', 'cursor-not-allowed');
        });
      }

      if (passwordInput && togglePassword && eyeOpen && eyeClosed) {
        togglePassword.addEventListener('click', function(e) {
          e.preventDefault();
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          if (type === 'text') {
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
          } else {
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
          }
        });
      }
    });
  </script>

  @include('partials.session-guard')
</body>
</html>