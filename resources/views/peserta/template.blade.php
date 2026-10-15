<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSB {{ config('client.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset(config('client.logo')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dropdown-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .dropdown-content.active {
            max-height: 500px;
            transition: max-height 0.3s ease-in;
        }
        .menu-item:hover {
            background-color: rgba(34, 197, 94, 0.1);
        }
        .user-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        .user-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            overflow: hidden;
            z-index: 50;
        }
        .sidebar-content {
            height: calc(100vh - 140px);
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sidebar-content::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-content::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        .sidebar-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }
        .sidebar-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        .main-content {
            margin-left: 256px;
            height: 100vh;
            overflow-y: auto;
        }
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .mobile-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <div id="mobile-overlay" class="mobile-overlay md:hidden"></div>
        
        <div id="sidebar" class="sidebar w-64 bg-green-600 text-white">
            <div class="p-6 text-center border-b border-green-500">
                <div class="w-16 h-16 bg-white rounded-full mx-auto mb-3 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset(config('client.logo')) }}" alt="Logo {{ config('client.name') }}" class="w-14 h-14 object-contain">
                </div>
                <h3 class="font-semibold text-lg">Peserta</h3>
            </div>

            <div class="sidebar-content">
                <nav class="mt-2 pb-4">
                    <ul class="space-y-1">
                        <li>
                            <a href="{{url('pages/dashboard')}}" class="menu-link flex items-center px-6 py-3 active" data-menu="dashboard">
                                <i class="fas fa-bookmark mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">DASHBOARD</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{url('/PmbMstPendaftarans/lengkapi_data')}}" class="menu-link flex items-center px-6 py-3" data-menu="lengkapi-data">
                                <i class="fas fa-user mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">LENGKAPI DATA</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="main-content flex-1">
            <header class="bg-white shadow-sm border-b border-gray-200 px-4 py-4 lg:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-gray-800 p-2 mr-4">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <div>
                            <p class="font-bold">
                                {{ Auth::user()->peserta->nama_peserta }}
                                <span class="text-gray-500 text-sm">({{ Auth::user()->peserta->no_pendaftaran }})</span>
                            </p>
                        </div>
                    </div>

                    <div class="relative">
                        <button id="user-menu-btn" class="text-gray-600 hover:text-gray-800 p-2">
                            <i class="fas fa-ellipsis-v text-lg"></i>
                        </button>
                        
                        <div id="user-menu" class="user-menu absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="font-semibold text-gray-800">Menu</p>
                            </div>

                            <form method="POST" action="{{ url('/ServiceLogout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors text-left">
                                    <i class="fas fa-user text-green-500 mr-3"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

           @yield('content')
        </div>
    </div>
<script src="https://cdn.tailwindcss.com"></script>
    <script>
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userMenu = document.getElementById('user-menu');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');

        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target) && !userMenuBtn.contains(e.target)) {
                userMenu.classList.remove('show');
            }
        });

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('show');
                mobileOverlay.classList.toggle('show');
            });
        }

        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                mobileOverlay.classList.remove('show');
            });
        }

        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const dropdown = document.getElementById(targetId);
                const arrow = button.querySelector('.dropdown-arrow');
                
                document.querySelectorAll('.dropdown-content').forEach(content => {
                    if (content.id !== targetId) {
                        content.classList.remove('active');
                        const otherArrow = document.querySelector(`[data-target="${content.id}"] .dropdown-arrow`);
                        if (otherArrow) {
                            otherArrow.style.transform = 'rotate(0deg)';
                        }
                    }
                });

                dropdown.classList.toggle('active');
                
                if (dropdown.classList.contains('active')) {
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    arrow.style.transform = 'rotate(0deg)';
                }
            });
        });

        const ctx = document.getElementById('statisticsChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Pendaftar',
                        data: [0, 0, 0, 0, 10, 25, 45, 65, 75, 60, 40, 20],
                        fill: true,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Bayar Pendaftaran',
                        data: [0, 0, 0, 0, 5, 15, 30, 50, 60, 45, 25, 10],
                        fill: true,
                        backgroundColor: 'rgba(54, 54, 54, 0.8)',
                        borderColor: 'rgba(54, 54, 54, 1)',
                        borderWidth: 2,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 80,
                        ticks: {
                            stepSize: 20
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        window.addEventListener('resize', () => {
            chart.resize();
        });

        console.log('PMB Dashboard loaded successfully');
    </script>
    @include('partials.session-guard')
</body>
</html>