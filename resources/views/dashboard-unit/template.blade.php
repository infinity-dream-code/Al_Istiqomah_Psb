<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSB {{ config('client.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset(config('client.logo')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 256px;
            height: 100vh;
            background-color: #16a34a;
            z-index: 40;
            overflow: hidden;
        }
        
        .sidebar-content {
            height: calc(100vh - 140px);
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .sidebar-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .sidebar-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        .main-content {
            margin-left: 256px;
            min-height: 100vh;
            width: calc(100% - 256px);
            background-color: #f9fafb;
        }
        
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

        .menu-item.active {
            background-color: #15803d;
            border-left: 4px solid white;
        }

        .submenu-item.active {
            background-color: #15803d;
            font-weight: 600;
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
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 30;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
                z-index: 50;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div id="sidebar-overlay" class="sidebar-overlay lg:hidden"></div>
    
    <div id="sidebar" class="sidebar text-white">
        <div class="p-6 text-center border-b border-green-500">
            <div class="w-16 h-16 bg-white rounded-full mx-auto mb-3 flex items-center justify-center overflow-hidden">
                <img src="{{ asset(config('client.logo')) }}" alt="Logo {{ config('client.name') }}" class="w-14 h-14 object-contain">
            </div>
            <h3 class="font-semibold text-lg">{{ ucwords(str_replace('-', ' ', $role ?? 'Admin')) }}</h3>
        </div>

        <div class="sidebar-content">
            <nav class="mt-2 pb-4">
                <ul class="space-y-1">
                    @if(in_array('dashboard', $menus ?? []))
                    <li>
                        <a href="{{ url('/pages/display/home/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="menu-item flex items-center px-6 py-3 text-white font-medium" data-menu="dashboard">
                            <i class="fas fa-bookmark mr-3 w-4 text-center"></i>
                            <span class="text-sm font-semibold">DASHBOARD</span>
                        </a>
                    </li>
                    @endif

                    @if(in_array('master data', $menus ?? []))
                    <li>
                        <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="masterdata-dropdown" data-menu="masterdata">
                            <div class="flex items-center">
                                <i class="fas fa-database mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">MASTER DATA</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                        </button>
                        <div id="masterdata-dropdown" class="dropdown-content bg-green-700">
                           <a href="{{ url('PmbMstPendaftarans/master-sekolah/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="master-sekolah">Jurusan</a>
                        </div>
                    </li>
                    @endif

                    @if(in_array('setting psb', $menus ?? []))
                    <li>
                        <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="setting-dropdown" data-menu="setting">
                            <div class="flex items-center">
                                <i class="fas fa-cogs mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">SETTING PSB</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                        </button>
                       <div id="setting-dropdown" class="dropdown-content bg-green-700">
                            <a href="{{ url('/PmbMstPendaftarans/setting-harga/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="setting-harga">Master Harga</a>
                            <a href="{{ url('/PmbMstPendaftarans/setting-pendaftaran/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="setting-pendaftaran">On/Off Pendaftaran</a>
                        </div>
                    </li>
                    @endif

                    @if(in_array('kelengkapan', $menus ?? []))
                    <li>
                        <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="kelengkapan-dropdown" data-menu="kelengkapan">
                            <div class="flex items-center">
                                <i class="fas fa-folder mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">KELENGKAPAN</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                        </button>
                        <div id="kelengkapan-dropdown" class="dropdown-content bg-green-700">
                            <a href="{{ url('PmbMstPendaftarans/cek_berkas_pembayaran/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="cek_berkas_pembayaran">Berkas & Pembayaran</a>
                        </div>
                    </li>
                    @endif

                    @if(in_array('ujian', $menus ?? []))
                    <li>
                        <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="ujian-dropdown" data-menu="ujian">
                            <div class="flex items-center">
                                <i class="fas fa-clipboard mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">UJIAN</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                        </button>
                        <div id="ujian-dropdown" class="dropdown-content bg-green-700">
                            <a href="{{ url('PmbMstPendaftarans/cek_berkas_set_ujian/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="cek_berkas_set_ujian">Set Kartu Ujian</a>
                            <a href="{{ url('PmbMstPendaftarans/cetak_kartu_ujian_reguler/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="cetak_kartu_ujian_reguler">Cetak Kartu Ujian</a>
                            <a href="{{ url('PmbMstPendaftarans/edit_jadwal_ujian/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="edit_jadwal_ujian">Edit Jadwal Ujian</a>
                        </div>
                    </li>
                    @endif

                    @if(in_array('kelulusan', $menus ?? []))
                    <li>
                        <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="kelulusan-dropdown" data-menu="kelulusan">
                            <div class="flex items-center">
                                <i class="fas fa-graduation-cap mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">KELULUSAN</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                        </button>
                        <div id="kelulusan-dropdown" class="dropdown-content bg-green-700">
                            <a href="{{ url('PmbMstPendaftarans/set_kelulusan/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="set_kelulusan">Set Kelulusan</a>
                        </div>
                    </li>
                    @endif

                    @if(in_array('registrasi', $menus ?? []))
                    <li>
                        <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="registrasi-dropdown" data-menu="registrasi">
                            <div class="flex items-center">
                                <i class="fas fa-user-plus mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">REGISTRASI</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                        </button>
                        <div id="registrasi-dropdown" class="dropdown-content bg-green-700">
                            <a href="{{ url('PmbMstPendaftarans/tagihan_daful/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="tagihan_daful">Tagihan Daftar Ulang</a>
                            <a href="{{ url('PmbMstPendaftarans/registrasi-lunas/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="registrasi-lunas">Registrasi Daftar Ulang Lunas</a>
                            <a href="{{ url('PmbMstPendaftarans/registrasi-cekstatus/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="registrasi-cekstatus">Cek Status</a>
                        </div>
                    </li>
                    @endif

                    @if(in_array('report', $menus ?? []))
                    <li>
                        <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="report-dropdown" data-menu="report">
                            <div class="flex items-center">
                                <i class="fas fa-chart-line mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">REPORT</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                        </button>
                       <div id="report-dropdown" class="dropdown-content bg-green-700">
    <a href="{{ url('PmbMstPendaftarans/rekap-jumlah-pendaftar/' . str_replace(' ', '-', ($role ?? 'admin'))) }}"
       class="submenu-item block px-12 py-2 hover:bg-green-800"
       data-submenu="rekap-jumlah-pendaftar">
       Rekap Jumlah Pendaftar
    </a>

    <a href="{{ url('PmbMstPendaftarans/rekap-lunas-pendaftaran/' . str_replace(' ', '-', ($role ?? 'admin'))) }}"
       class="submenu-item block px-12 py-2 hover:bg-green-800"
       data-submenu="rekap-lunas-pendaftaran">
       Rekap Lunas Pendaftaran
    </a>

    <a href="{{ url('PmbMstPendaftarans/rekap-lunas-registrasi/' . str_replace(' ', '-', ($role ?? 'admin'))) }}"
       class="submenu-item block px-12 py-2 hover:bg-green-800"
       data-submenu="rekap-lunas-registrasi">
       Rekap Lunas Registrasi Daftar Ulang
    </a>

    <a href="{{ url('PmbMstPendaftarans/siswa-per-provinsi/' . str_replace(' ', '-', ($role ?? 'admin'))) }}"
       class="submenu-item block px-12 py-2 hover:bg-green-800"
       data-submenu="siswa-per-provinsi">
       Siswa Per Provinsi
    </a>

    <a href="{{ url('PmbMstPendaftarans/siswa-per-kota/' . str_replace(' ', '-', ($role ?? 'admin'))) }}"
       class="submenu-item block px-12 py-2 hover:bg-green-800"
       data-submenu="siswa-per-kota">
       Siswa Per Kota
    </a>

    <a href="{{ url('PmbMstPendaftarans/siswa-per-prodi/' . str_replace(' ', '-', ($role ?? 'admin'))) }}"
       class="submenu-item block px-12 py-2 hover:bg-green-800"
       data-submenu="siswa-per-prodi">
       Siswa Per Jurusan
    </a>

    <a href="{{ url('PmbMstPendaftarans/siswa-per-sekolah/' . str_replace(' ', '-', ($role ?? 'admin'))) }}"
       class="submenu-item block px-12 py-2 hover:bg-green-800"
       data-submenu="siswa-per-sekolah">
       Siswa Per Sekolah Asal
    </a>

    <a href="{{ url('PmbMstPendaftarans/export-detail-biaya/' . str_replace(' ', '-', ($role ?? 'admin'))) }}"
       class="submenu-item block px-12 py-2 hover:bg-green-800"
       data-submenu="export-detail-biaya">
       Export Detail Biaya
    </a>

    <a href="{{ url('PmbMstPendaftarans/export-all/' . str_replace(' ', '-', ($role ?? 'admin'))) }}"
       class="submenu-item block px-12 py-2 hover:bg-green-800"
       data-submenu="export-all">
       Export All
    </a>
</div>
                    </li>
                    @endif

                    @if(in_array('group & pengguna', $menus ?? []))
                    <li>
                        <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="group-dropdown" data-menu="group">
                            <div class="flex items-center">
                                <i class="fas fa-users mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">GROUP & PENGGUNA</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                        </button>
                        <div id="group-dropdown" class="dropdown-content bg-green-700">
                            <a href="{{ url('PmbMstPendaftarans/users/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="users">User</a>
                        </div>
                    </li>
                    @endif

                    @if(in_array('kelola peserta', $menus ?? []))
                    <li>
                        <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="edit-dropdown" data-menu="edit">
                            <div class="flex items-center">
                                <i class="fas fa-user mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">KELOLA PESERTA</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                        </button>
                        <div id="edit-dropdown" class="dropdown-content bg-green-700">
                            <a href="{{ url('PmbMstPendaftarans/list-user/' . str_replace(' ', '-', $role ?? 'admin')) }}" class="submenu-item block px-12 py-2 hover:bg-green-800" data-submenu="list-user">List Pendaftar</a>
                        </div>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>

    <div class="main-content">
        <header class="bg-white shadow-sm border-b border-gray-200 px-4 py-4 lg:px-6 sticky top-0 z-30">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <button id="hamburger-btn" class="lg:hidden text-gray-600 hover:text-gray-800 p-2 mr-3">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
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
                                <i class="fas fa-sign-out-alt text-green-500 mr-3"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        @yield('content')
    </div>

    <script>
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userMenu = document.getElementById('user-menu');
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        if (userMenuBtn && userMenu) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenu.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target) && !userMenuBtn.contains(e.target)) {
                    userMenu.classList.remove('show');
                }
            });
        }

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        }

        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', toggleSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });

        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const dropdown = document.getElementById(targetId);
                const arrow = button.querySelector('.dropdown-arrow');
                
                if (dropdown && arrow) {
                    dropdown.classList.toggle('active');
                    
                    if (dropdown.classList.contains('active')) {
                        arrow.style.transform = 'rotate(180deg)';
                    } else {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                }
            });
        });

        function setActiveMenu() {
            const currentPath = window.location.pathname;
            const menuItems = document.querySelectorAll('.menu-item');
            const submenuItems = document.querySelectorAll('.submenu-item');
            
            menuItems.forEach(item => {
                item.classList.remove('active');
            });
            
            submenuItems.forEach(item => {
                item.classList.remove('active');
            });
            
            if (currentPath.includes('/pages/display/home/')) {
                const dashboardMenu = document.querySelector('[data-menu="dashboard"]');
                if (dashboardMenu) {
                    dashboardMenu.classList.add('active');
                }
            } else {
                let foundActive = false;
                
                submenuItems.forEach(item => {
                    const submenuKey = item.getAttribute('data-submenu');
                    if (submenuKey && currentPath.includes(submenuKey)) {
                        item.classList.add('active');
                        foundActive = true;
                        
                        const parentDropdown = item.closest('.dropdown-content');
                        if (parentDropdown) {
                            const dropdownId = parentDropdown.getAttribute('id');
                            const parentButton = document.querySelector(`[data-target="${dropdownId}"]`);
                            
                            if (parentButton) {
                                parentButton.classList.add('active');
                                parentDropdown.classList.add('active');
                                const arrow = parentButton.querySelector('.dropdown-arrow');
                                if (arrow) {
                                    arrow.style.transform = 'rotate(180deg)';
                                }
                            }
                        }
                    }
                });
                
                if (!foundActive) {
                    const dashboardMenu = document.querySelector('[data-menu="dashboard"]');
                    if (dashboardMenu) {
                        dashboardMenu.classList.add('active');
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            setActiveMenu();
        });
    </script>
    @include('partials.session-guard')
</body>
</html>