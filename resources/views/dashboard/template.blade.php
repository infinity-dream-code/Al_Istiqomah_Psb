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
            z-index: 40;
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
            height: 100vh;
            overflow-y: auto;
        }
        
        @media (min-width: 1024px) {
            .main-content {
                margin-left: 256px;
            }
            .sidebar {
                display: block !important;
            }
            .hamburger-btn {
                display: none;
            }
        }
        
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
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
    </style>
</head>
<body class="bg-gray-50">
    <div id="sidebar-overlay" class="sidebar-overlay lg:hidden"></div>
    
    <div class="flex min-h-screen">
        <div id="sidebar" class="sidebar w-64 bg-green-600 text-white">
            <div class="p-6 text-center border-b border-green-500">
                <div class="w-16 h-16 bg-white rounded-full mx-auto mb-3 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset(config('client.logo')) }}" alt="Logo {{ config('client.name') }}" class="w-14 h-14 object-contain">
                </div>
                <h3 class="font-semibold text-lg">Admin</h3>
            </div>

            <div class="sidebar-content">
                <nav class="mt-2 pb-4">
                    <ul class="space-y-1">
                        <li>
                            <a href="{{url('/pages/display/home')}}" class="menu-item flex items-center px-6 py-3 text-white font-medium" data-menu="dashboard">
                                <i class="fas fa-bookmark mr-3 w-4 text-center"></i>
                                <span class="text-sm font-semibold">DASHBOARD</span>
                            </a>
                        </li>
                        <li>
                            <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="masterdata-dropdown" data-menu="masterdata">
                                <div class="flex items-center">
                                    <i class="fas fa-database mr-3 w-4 text-center"></i>
                                    <span class="text-sm font-semibold">MASTER DATA</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                            </button>
                            <div id="masterdata-dropdown" class="dropdown-content bg-green-700">
                               <a href="{{ url('/PmbMstPendaftarans/master-tahun-akademik') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="masterdata">Tahun Akademik</a>
<a href="{{ url('/PmbMstPendaftarans/master-gelombang') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="masterdata">Gelombang</a>
<a href="{{ url('/PmbMstPendaftarans/master-jalur') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="masterdata">Jalur</a>
<a href="{{ url('/PmbMstPendaftarans/master-unit') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="masterdata">Sekolah</a><a href="{{ url('/PmbMstPendaftarans/master-ujian') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="masterdata">Ujian</a>
<a href="{{ url('/PmbMstPendaftarans/master-ruang') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="masterdata">Ruang</a>
 <a href="{{ url('/PmbMstPendaftarans/master-role') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="masterdata">Role</a>

</div>
                        </li>
<li>
    <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="psb-dropdown" data-menu="psb">
        <div class="flex items-center">
            <i class="fas fa-cogs mr-3 w-4 text-center"></i>
            <span class="text-sm font-semibold">SETTING PSB</span>
        </div>
        <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
    </button>
    <div id="psb-dropdown" class="dropdown-content bg-green-700">
        <a href="{{ url('/PmbMstPendaftarans/setting-gelombang') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="psb">Gelombang PSB</a>
        <a href="{{ url('/PmbMstPendaftarans/setting-harga') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="psb">Master Harga</a>
        <a href="{{ url('/PmbMstPendaftarans/setting-pendaftaran') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="psb">On/Off Pendaftaran</a>
        <a href="{{ url('/PmbMstPendaftarans/setting-slider') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="psb">Image Slider</a>
        <a href="{{ url('/PmbMstPendaftarans/setting-informasi') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="psb">Informasi</a>
        <a href="{{ url('/PmbMstPendaftarans/setting-brosur') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="psb">Brosur</a>
    </div>
</li>


                        <li>
                            <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="kelengkapan-dropdown" data-menu="kelengkapan">
                                <div class="flex items-center">
                                    <i class="fas fa-folder mr-3 w-4 text-center"></i>
                                    <span class="text-sm font-semibold">KELENGKAPAN</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                            </button>
                            <div id="kelengkapan-dropdown" class="dropdown-content bg-green-700">
                                <a href="{{ url('/PmbMstPendaftarans/cek_berkas_pembayaran') }}" 
   class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" 
   data-menu="kelengkapan">
   Berkas & Pembayaran
</a>

 </div>
                        </li>
                        <li>
                            <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="ujian-dropdown" data-menu="ujian">
                                <div class="flex items-center">
                                    <i class="fas fa-clipboard mr-3 w-4 text-center"></i>
                                    <span class="text-sm font-semibold">UJIAN</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                            </button>
                            <div id="ujian-dropdown" class="dropdown-content bg-green-700">
                               <a href="{{ url('PmbMstPendaftarans/cek_berkas_set_ujian') }}" 
   class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" 
   data-menu="ujian">
   Set Kartu Ujian
</a>

<a href="{{ url('PmbMstPendaftarans/cetak_kartu_ujian_reguler') }}" 
   class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" 
   data-menu="ujian">
   Cetak Kartu Ujian
</a>

<a href="{{ url('PmbMstPendaftarans/edit_jadwal_ujian') }}" 
   class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" 
   data-menu="ujian">
   Edit Jadwal Ujian
</a>
</div>
                        </li>
                        <li>
                            <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="kelulusan-dropdown" data-menu="kelulusan">
                                <div class="flex items-center">
                                    <i class="fas fa-user-friends mr-3 w-4 text-center"></i>
                                    <span class="text-sm font-semibold">KELULUSAN</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                            </button>
                            <div id="kelulusan-dropdown" class="dropdown-content bg-green-700">
                                <a href="{{url('PmbMstPendaftarans/set_kelulusan-peserta')}}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="kelulusan">Set Kelulusan</a>
                            </div>
                        </li>
                        <li>
                            <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="registrasi-dropdown" data-menu="registrasi">
                                <div class="flex items-center">
                                    <i class="fas fa-user-plus mr-3 w-4 text-center"></i>
                                    <span class="text-sm font-semibold">REGISTRASI</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                            </button>
                            <div id="registrasi-dropdown" class="dropdown-content bg-green-700">
                                <a href="{{url('PmbMstPendaftarans/registrasi-lunas')}}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="registrasi">Registrasi Daftar Ulang Lunas</a>
                                <a href="{{url('PmbMstPendaftarans/registrasi-cekstatus')}}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="registrasi">Cek Status</a>
                                  <a href="{{ url('/PmbMstPendaftarans/tagihan_daful') }}" 
   class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" 
   data-menu="registrasi">
   Tagihan Daftar Ulang
</a>
                            </div>
                        </li>
                        
                        <li>
                            <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="report-dropdown" data-menu="report">
                                <div class="flex items-center">
                                    <i class="fas fa-chart-line mr-3 w-4 text-center"></i>
                                    <span class="text-sm font-semibold">REPORT</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                            </button>
                            <div id="report-dropdown" class="dropdown-content bg-green-700">
                                <a href="{{ route('report.rekapJumlahPendaftar') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="report">Rekap Jumlah Pendaftar</a>
                                <a href="{{ route('report.rekapLunasPendaftaran') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="report">Rekap Lunas Registrasi</a>
                                <a href="{{ route('report.rekapLunasRegistrasi') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="report">Rekap Lunas Registrasi Daftar Ulang</a>
                                <a href="{{ route('report.siswaProvinsi') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="report">Siswa Per Provinsi</a>
                                <a href="{{ route('report.siswaKota') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="report">Siswa Per Kota</a>                                <a href="{{ route('report.siswaSekolah') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="report">Siswa Per Sekolah Asal</a>
                                <a href="{{ route('report.exportDetailBiaya') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="report">Export Detail Biaya</a>
                                <a href="{{ route('report.exportAll') }}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="report">Export All</a>
                            </div>
                        </li>
                        <li>
                            <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="group-dropdown" data-menu="group">
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-3 w-4 text-center"></i>
                                    <span class="text-sm font-semibold">GROUP & PENGGUNA</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                            </button>
                            <div id="group-dropdown" class="dropdown-content bg-green-700">
                                <a href="{{url('PmbMstPendaftarans/users')}}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="group">Users</a>
                            </div>
                        </li>
                        <li>
                            <button class="menu-item w-full flex items-center justify-between px-6 py-3 text-white font-medium dropdown-toggle hover:bg-green-700 transition-colors" data-target="edit-dropdown" data-menu="edit">
                                <div class="flex items-center">
                                    <i class="fas fa-edit mr-3 w-4 text-center"></i>
                                    <span class="text-sm font-semibold">Kelola Peserta</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform dropdown-arrow"></i>
                            </button>
                            <div id="edit-dropdown" class="dropdown-content bg-green-700">
                                <a href="{{url('PmbMstPendaftarans/list-user')}}" class="block px-12 py-2 text-sm hover:bg-green-800 transition-colors" data-menu="edit">List Pendaftar</a>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="main-content flex-1">
            <header class="bg-white shadow-sm border-b border-gray-200 px-4 py-4 lg:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button id="hamburger-btn" class="hamburger-btn lg:hidden text-gray-600 hover:text-gray-800 p-2 mr-3">
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
        function setActiveMenu() {
            const currentPath = window.location.pathname;
            const menuItems = document.querySelectorAll('.menu-item');
            const subMenuItems = document.querySelectorAll('.dropdown-content a');
            const dropdownButtons = document.querySelectorAll('.dropdown-toggle');
            const dropdowns = document.querySelectorAll('.dropdown-content');
            const arrows = document.querySelectorAll('.dropdown-arrow');
            
            // Reset semua menu ke state tidak active
            menuItems.forEach(item => {
                item.classList.remove('bg-green-700', 'border-l-4', 'border-white');
            });
            
            dropdownButtons.forEach(button => {
                button.classList.remove('bg-green-700', 'border-l-4', 'border-white');
            });
            
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
            
            arrows.forEach(arrow => {
                arrow.style.transform = 'rotate(0deg)';
            });
            
            let activeFound = false;
            
            // Cek sub-menu dulu (menu di dalam dropdown) - dengan pencocokan yang lebih ketat
            subMenuItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href) {
                    let cleanHref = href.split('?')[0];
                    
                    if (cleanHref.includes('{{') && cleanHref.includes('}}')) {
                        cleanHref = cleanHref.replace(/\{\{url\('([^']+)'\)\}\}/g, '$1');
                        cleanHref = cleanHref.replace(/\{\{[^}]+\}\}/g, '');
                    }
                    
                    // Pencocokan yang lebih tepat - harus sama persis atau path mengandung bagian unik dari href
                    if (currentPath === cleanHref || 
                        (cleanHref.length > 10 && currentPath.includes(cleanHref)) ||
                        (cleanHref.includes('cek_berkas_set_ujian') && currentPath.includes('cek_berkas_set_ujian')) ||
                        (cleanHref.includes('cetak_kartu_ujian_reguler') && currentPath.includes('cetak_kartu_ujian_reguler')) ||
                        (cleanHref.includes('edit_jadwal_ujian') && currentPath.includes('edit_jadwal_ujian')) ||
                        (cleanHref.includes('set_kelulusan') && currentPath.includes('set_kelulusan')) ||
                        (cleanHref.includes('registrasi-lunas') && currentPath.includes('registrasi-lunas')) ||
                        (cleanHref.includes('registrasi-cekstatus') && currentPath.includes('registrasi-cekstatus'))
                    ) {
                        const menuType = item.getAttribute('data-menu');
                        if (menuType) {
                            const dropdown = document.getElementById(`${menuType}-dropdown`);
                            const button = document.querySelector(`[data-target="${menuType}-dropdown"]`);
                            const arrow = button?.querySelector('.dropdown-arrow');
                            
                            if (dropdown) {
                                dropdown.classList.add('active');
                            }
                            if (button) {
                                button.classList.add('bg-green-700', 'border-l-4', 'border-white');
                            }
                            if (arrow) {
                                arrow.style.transform = 'rotate(180deg)';
                            }
                            activeFound = true;
                            return; // Keluar dari loop jika sudah ketemu
                        }
                    }
                }
            });
            
            // Kalau tidak ketemu di sub-menu, cek menu utama (tapi skip dashboard)
            if (!activeFound) {
                menuItems.forEach(item => {
                    const href = item.getAttribute('href');
                    const menuType = item.getAttribute('data-menu');
                    if (href && menuType !== 'dashboard') {
                        const cleanHref = href.split('?')[0];
                        if (currentPath === cleanHref) {
                            item.classList.add('bg-green-700', 'border-l-4', 'border-white');
                            activeFound = true;
                            return; // Keluar dari loop jika sudah ketemu
                        }
                    }
                });
            }
            if (!activeFound && currentPath === '/pages/display/home') {
                const dashboardMenu = document.querySelector('[data-menu="dashboard"]');
                if (dashboardMenu) {
                    dashboardMenu.classList.add('bg-green-700', 'border-l-4', 'border-white');
                }
            }
        }

        const userMenuBtn = document.getElementById('user-menu-btn');
        const userMenu = document.getElementById('user-menu');

        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target) && !userMenuBtn.contains(e.target)) {
                userMenu.classList.remove('show');
            }
        });

        const hamburgerBtn = document.getElementById('hamburger-btn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        }

        hamburgerBtn.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);

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
                
                dropdown.classList.toggle('active');
                
                if (dropdown.classList.contains('active')) {
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    arrow.style.transform = 'rotate(0deg)';
                }
            });
        });

        document.querySelectorAll('a[data-menu], button[data-menu]').forEach(item => {
            item.addEventListener('click', function() {
                const menuType = this.getAttribute('data-menu');
                if (menuType) {
                    localStorage.setItem('activeMenu', menuType);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            setActiveMenu();
            
            const savedActiveMenu = localStorage.getItem('activeMenu');
            if (savedActiveMenu) {
                const menuItems = document.querySelectorAll(`[data-menu="${savedActiveMenu}"]`);
                menuItems.forEach(item => {
                    if (item.tagName === 'BUTTON' && item.classList.contains('dropdown-toggle')) {
                        const targetId = item.getAttribute('data-target');
                        const dropdown = document.getElementById(targetId);
                        const arrow = item.querySelector('.dropdown-arrow');
                        
                        if (dropdown) {
                            dropdown.classList.add('active');
                        }
                        if (arrow) {
                            arrow.style.transform = 'rotate(180deg)';
                        }
                        item.classList.add('bg-green-700', 'border-l-4', 'border-white');
                    }
                });
            }
        });

        if (document.getElementById('statisticsChart')) {
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
        }

        console.log('PMB Dashboard loaded successfully');
    </script>
    @include('partials.session-guard')
</body>
</html>