<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MasterJalurController;
use App\Http\Controllers\MasterBiayaPendaftaranController;
use App\Http\Controllers\MasterProdiController;
use App\Http\Controllers\MasterRuangController;
use App\Http\Controllers\MasterRoleController;
use App\Http\Controllers\MasterGelombangController;
use App\Http\Controllers\BayarController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PembayaranSekolahController;
use App\Http\Controllers\MasterHargaController;
use App\Http\Controllers\MasterUjianController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\MasterAkademikController;
use App\Http\Controllers\ImagesliderController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\BrosurController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'index1']);
Route::get('PmbMstPendaftarans/cetak_kartu/{no_pendaftaran}',[UjianController::class, 'CetakKartu'])->name('ujian.showKartu');

// Registrasi
Route::get('/enroll', [RegisterController::class, 'index']);
Route::post('/enroll', [RegisterController::class, 'enroll'])->name('enroll');
Route::get('/enroll/prodi/{id}', [RegisterController::class, 'getProdi'])->name('enroll.getProdi');

// PMB
Route::get('/PmbMstPendaftarans/success_enroll/{token}', [RegisterController::class, 'success'])->name('pmb.success');
Route::get('/PmbMstPendaftarans/cek_tagihan/{no_pendaftaran}', [RegisterController::class, 'cekTagihan'])->name('pmb.cekTagihan');

// Login & Logout
Route::get('/ServiceLogin', [LoginController::class, 'index'])->name('login.form');
Route::post('/ServiceLogin', [LoginController::class, 'login'])->name('login');
// Logout hanya via POST (klik tombol Logout) — GET tidak pernah mematikan session
Route::post('/ServiceLogout', [LoginController::class, 'logout'])->name('logout');
Route::get('/ServiceLogout', function () {
    return redirect('/ServiceLogin');
});

// Keep-alive session + refresh CSRF (tanpa login ulang / tanpa pesan sesi)
Route::get('/session/keepalive', function (\Illuminate\Http\Request $request) {
    // Jangan rewrite session kalau belum login — kurangi race file session.
    if ($request->hasSession() && $request->session()->isStarted()) {
        $request->session()->put('_keepalive_at', now()->timestamp);
    }

    // Session hilang di tengah jalan: buat ulang dari cookie cadangan sesuai halaman yang sedang dibuka.
    if (!auth()->check() && $request->filled('path')) {
        \App\Http\Middleware\RestoreAuthFromCookie::attemptForPath($request, (string) $request->query('path'));
    }

    return response()->json([
        'ok' => true,
        'auth' => auth()->check(),
        'token' => csrf_token(),
    ])->header('Cache-Control', 'no-store, private');
})->name('session.keepalive');

/*
|--------------------------------------------------------------------------
| Peserta Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:peserta'])->group(function () {
    Route::get('/pages/dashboard', [PesertaController::class, 'index'])->name('peserta.dashboard');

    Route::get('/PmbMstPendaftarans/lengkapi_data', [PesertaController::class, 'lengkapi_data']);
    Route::post('/PmbMstPendaftarans/lengkapi_data', [PesertaController::class, 'simpan_data']);

    Route::post('/peserta/simpan-bantuan', [PesertaController::class, 'simpan_bantuan'])->name('peserta.simpan_bantuan');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/pages/display/home/{role}', [DashboardController::class, 'role']);

    Route::get('/PmbMstPendaftarans/cek_berkas_pembayaran/{role}', [PembayaranController::class, 'cekBerkas1']);
  
    Route::get('/api/pesertaku/{id}', [PembayaranController::class, 'detail2'])
    ->name('peserta.detail2');

 Route::get('PmbMstPendaftarans/cek_berkas_set_ujian/{role}', [UjianController::class, 'cekBerkasSetUjian2'])
        ->name('ujian.cekBerkasSetUjian1');

  Route::post('PmbMstPendaftarans/Set-Ujiann', [UjianController::class, 'simpan1'])->name('ujian.simpan1');

    Route::get('PmbMstPendaftarans/cetak_kartu_ujian_reguler/{role}', [UjianController::class, 'cetakKartuUjian1'])
        ->name('ujian.cetakKartuUjianReguler1');

  Route::get('PmbMstPendaftarans/cetak_kartu/{role}/{no_pendaftaran}',[UjianController::class, 'CetakKartu1'])->name('ujian.showKartu1');

   Route::get('PmbMstPendaftarans/edit_jadwal_ujian/{role}', [UjianController::class, 'editJadwalUjian1'])
        ->name('ujian.editJadwalUjian1');

Route::post('/PmbMstPendaftarans/update_jadwal_ujian/{role}', [UjianController::class, 'updateJadwalUjian1'])
    ->name('pmb.updateJadwalUjian1');

    Route::get('/PmbMstPendaftarans/tagihan_daful/{role}', [TagihanController::class, 'index1']);
 
    Route::get('/PmbMstPendaftarans/registrasi-lunas/{role}', [RegisterController::class, 'registrasiLunas1'])
     ->name('registrasi.lunas1');

     Route::get('PmbMstPendaftarans/registrasi-cekstatus/{role}', [RegisterController::class, 'cekStatusIndex1'])->name('registrasi.cekstatus.index1');

     Route::post('PmbMstPendaftarans/registrasi-cekstatuss', [RegisterController::class, 'cekStatus1'])->name('registrasi.cekStatus1');

Route::get('/PmbMstPendaftarans/rekap-jumlah-pendaftar/{role}', [ReportController::class, 'rekapJumlahPendaftar1'])->name('report.rekapJumlahPendaftar1');
Route::get('/PmbMstPendaftarans/rekap-lunas-pendaftaran/{role}', [ReportController::class, 'rekapLunasPendaftaran1'])->name('report.rekapLunasPendaftaran1');
Route::get('/PmbMstPendaftarans/rekap-lunas-registrasi/{role}', [ReportController::class, 'rekapLunasRegistrasi1'])->name('report.rekapLunasRegistrasi1');
Route::get('/PmbMstPendaftarans/siswa-per-provinsi/{role}', [ReportController::class, 'siswaPerProvinsi1'])->name('report.siswaProvinsi1');
Route::get('/PmbMstPendaftarans/siswa-per-kota/{role}', [ReportController::class, 'siswaPerKota1'])->name('report.siswaKota1');
Route::get('/PmbMstPendaftarans/siswa-per-prodi/{role}', [ReportController::class, 'siswaPerProdi1'])->name('report.siswaProdi1');
Route::get('/PmbMstPendaftarans/siswa-per-sekolah/{role}', [ReportController::class, 'siswaPerSekolah1'])->name('report.siswaSekolah1');
Route::get('/PmbMstPendaftarans/export-detail-biaya/{role}', [ReportController::class, 'exportDetailBiaya1'])->name('report.exportDetailBiaya1');
Route::get('/PmbMstPendaftarans/export-all/{role}', [ReportController::class, 'exportAll1'])->name('report.exportAll1');
Route::get('/PmbMstPendaftarans/print_lp003/{role}/{ids}', [ReportController::class, 'printLp0031'])->name('report.printLp0031');
Route::get('/PmbMstPendaftarans/print_lp004/{role}/{ids}', [ReportController::class, 'printLp0041'])->name('report.printLp0041');
Route::get('/PmbMstPendaftarans/print_lp007/{role}/{ids}', [ReportController::class, 'printLp0071'])->name('report.printLp0071');
Route::get('/PmbMstPendaftarans/export-all-psb/{role}', [ReportController::class, 'exportAllpsb1'])->name('report.exportAllpsb1');

Route::get('PmbMstPendaftarans/list-user/{role}', [UserController::class, 'userlist1'])
    ->name('users.list1');

     Route::get('PmbMstPendaftarans/users/{id}/detail1', [UserController::class, 'showuser1']);

         Route::get('PmbMstPendaftarans/{role}/peserta/edit/{id}', [UserController::class, 'editpeserta1'])->name('peserta.edit1');

    Route::put('PmbMstPendaftarans/{role}/peserta/update/{id}', [UserController::class, 'updatepeserta1'])
    ->name('peserta.update1');

Route::get('/PmbMstPendaftarans/master-sekolah/{role}', [MasterProdiController::class, 'index1'])->name('master.sekolah1');
Route::get('/PmbMstPendaftarans/master-sekolah/add/{role}', [MasterProdiController::class, 'create1'])->name('master.sekolah.add1');
Route::post('/PmbMstPendaftarans/master-sekolah/store/{role}', [MasterProdiController::class, 'store1'])->name('master.sekolah.store1');
Route::delete('/PmbMstPendaftarans/master-sekolah/delete/{role}/{id}', [MasterProdiController::class, 'destroy1'])->name('master.sekolah.delete1');
Route::get('/PmbMstPendaftarans/master-sekolah/edit/{role}/{id}', [MasterProdiController::class, 'edit1'])->name('master.sekolah.edit1');
Route::put('/PmbMstPendaftarans/master-sekolah/update/{role}/{id}', [MasterProdiController::class, 'update1'])->name('master.sekolah.update1');

Route::get('PmbMstPendaftarans/setting-harga/{role}', [MasterHargaController::class, 'index1']);
Route::get('PmbRefMasterHargaPendaftarans/{role}/edit/{id}', [MasterHargaController::class, 'edit1'])->name('master-harga.edit1');
Route::put('PmbRefMasterHargaPendaftarans/{role}/update/{id}', [MasterHargaController::class, 'update1'])->name('master-harga.update1');

Route::get('PmbMstPendaftarans/setting-pendaftaran/{role}', [MasterBiayaPendaftaranController::class, 'index3']);
 Route::get('PmbMstPendaftarans/set_kelulusan/{role}', [UjianController::class, 'setKelulusan1'])
    ->name('pmb.set_kelulusan1');

      Route::get('/PmbMstPendaftarans/set_kelulusanku/{status}', [UjianController::class, 'setKelulusanPeserta1'])
    ->name('ujian.setKelulusan1');


    Route::get('PmbMstPendaftarans/users/{role}', [UserController::class, 'index1'])->name('users.index1');
Route::get('PmbMstPendaftarans/users/{role}/{id}/edit', [UserController::class, 'edit1'])->name('users.edit1');
Route::put('PmbMstPendaftarans/users/{role}/{id}', [UserController::class, 'update1'])->name('users.update1');
    

});

   

Route::get('/get-prodi-by-fakultas/{id}', [MasterHargaController::class, 'getProdiByFakultas']);

Route::get('PmbMstPendaftarans/validate_tagihan', [UjianController::class, 'validateTagihan']);

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

 Route::post('fakultas/toggle/{id}', [SettingsController::class, 'toggleFakultas'])->name('fakultas.toggle');


Route::post('/master-harga/toggle-active/{id}', [MasterHargaController::class, 'toggleActive'])
    ->name('master-harga.toggle-active');

Route::get('/detailRegistrasi/{encoded}', [UserController::class, 'detailRegistrasi'])
    ->name('detail.registrasi');

Route::get('/PmbMstPendaftarans/cetak_formulir/{encoded}', [UserController::class, 'cetakFormulir'])
    ->name('cetak.formulir');

Route::get('/PmbMstPendaftarans/cetak_info_enroll/{nama}/{no_pendaftaran}/{jalur}', [UserController::class, 'cetakInfoEnroll'])
    ->name('cetak.info.enroll');

    Route::get('/api/peserta/{id}', [PembayaranController::class, 'detail'])
        ->name('peserta.detail');


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/pages/display/home', [DashboardController::class, 'index']);
Route::post('PmbMstPendaftarans/Set-Ujian', [UjianController::class, 'simpan'])->name('ujian.simpan');

Route::get('/PmbMstPendaftarans/rekap-jumlah-pendaftar', [ReportController::class, 'rekapJumlahPendaftar'])
    ->name('report.rekapJumlahPendaftar');


Route::get('/PmbMstPendaftarans/rekap-lunas-pendaftaran', [ReportController::class, 'rekapLunasPendaftaran'])
    ->name('report.rekapLunasPendaftaran');

Route::get('/PmbMstPendaftarans/rekap-lunas-registrasi', [ReportController::class, 'rekapLunasRegistrasi'])
    ->name('report.rekapLunasRegistrasi');

Route::get('/PmbMstPendaftarans/siswa-per-provinsi', [ReportController::class, 'siswaPerProvinsi'])
    ->name('report.siswaProvinsi');

Route::get('/PmbMstPendaftarans/siswa-per-kota', [ReportController::class, 'siswaPerKota'])
    ->name('report.siswaKota');

Route::get('/PmbMstPendaftarans/siswa-per-prodi', [ReportController::class, 'siswaPerProdi'])
    ->name('report.siswaProdi');

Route::get('/PmbMstPendaftarans/siswa-per-sekolah', [ReportController::class, 'siswaPerSekolah'])
    ->name('report.siswaSekolah');

Route::get('/PmbMstPendaftarans/export-detail-biaya', [ReportController::class, 'exportDetailBiaya'])
    ->name('report.exportDetailBiaya');

Route::get('/PmbMstPendaftarans/export-all', [ReportController::class, 'exportAll'])
    ->name('report.exportAll');

    Route::get('/PmbMstPendaftarans/print_lp003/{ids}', [ReportController::class, 'printLp003'])
    ->name('report.printLp003');

        Route::get('/PmbMstPendaftarans/print_lp004/{ids}', [ReportController::class, 'printLp004'])
    ->name('report.printLp004');

     Route::get('/PmbMstPendaftarans/print_lp007/{ids}', [ReportController::class, 'printLp007'])
    ->name('report.printLp007');
    
Route::get('/PmbMstPendaftarans/export-all-psb', [ReportController::class, 'exportAllpsb'])
    ->name('report.exportAllpsb');

Route::get('get-temporary-detail/{jalur}/{fakultas}/{prodi}', [MasterHargaController::class, 'getTemporaryDetail']);

Route::get('PmbMstPendaftarans/users', [UserController::class, 'index'])->name('users.index');
Route::get('PmbMstPendaftarans/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('PmbMstPendaftarans/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('PmbMstPendaftarans/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('PmbMstPendaftarans/usersku/tambah', [UserController::class, 'tambahuser'])->name('users.adduser');
Route::post('PmbMstPendaftarans/users/store', [UserController::class, 'store'])->name('users.store');
Route::get('/api/get-prodi-by-fakultas/{id}', [UserController::class, 'getProdiByFakultas']);


Route::get('PmbMstPendaftarans/list-user', [UserController::class, 'userlist'])
    ->name('users.list');


    Route::get('PmbMstPendaftarans/peserta/edit/{id}', [UserController::class, 'editpeserta'])->name('peserta.edit');

    Route::put('PmbMstPendaftarans/peserta/update/{id}', [UserController::class, 'updatepeserta'])
    ->name('peserta.update');

    Route::get('PmbMstPendaftarans/users/{id}/detail', [UserController::class, 'showuser']);

    Route::get('/PmbMstPendaftarans/master-ruang', [MasterRuangController::class, 'index'])->name('master.ruang');
Route::get('/PmbMstPendaftarans/master-ruang/add', [MasterRuangController::class, 'create'])->name('master.ruang.add');
Route::post('/PmbMstPendaftarans/master-ruang/store', [MasterRuangController::class, 'store'])->name('master.ruang.store');
Route::get('/PmbMstPendaftarans/master-ruang/edit/{id}', [MasterRuangController::class, 'edit'])->name('master.ruang.edit');
Route::put('/PmbMstPendaftarans/master-ruang/update/{id}', [MasterRuangController::class, 'update'])->name('master.ruang.update');
Route::delete('/PmbMstPendaftarans/master-ruang/delete/{id}', [MasterRuangController::class, 'destroy'])->name('master.ruang.delete');

Route::get('/PmbMstPendaftarans/master-role', [MasterRoleController::class, 'index'])->name('master.role');
Route::get('/PmbMstPendaftarans/master-role/add', [MasterRoleController::class, 'create'])->name('master.role.add');
Route::post('/PmbMstPendaftarans/master-role/store', [MasterRoleController::class, 'store'])->name('master.role.store');
Route::get('/PmbMstPendaftarans/master-role/edit/{id}', [MasterRoleController::class, 'edit'])->name('master.role.edit');
Route::put('/PmbMstPendaftarans/master-role/update/{id}', [MasterRoleController::class, 'update'])->name('master.role.update');
Route::delete('/PmbMstPendaftarans/master-role/delete/{id}', [MasterRoleController::class, 'destroy'])->name('master.role.delete');

Route::get('/PmbMstPendaftarans/tagihan_daful', [TagihanController::class, 'index']);

//master unit/fakultas
Route::get('/PmbMstPendaftarans/master-unit', [MasterBiayaPendaftaranController::class, 'index'])->name('master.unit');
Route::get('/PmbMstPendaftarans/master-unit/add', [MasterBiayaPendaftaranController::class, 'create'])->name('master.unit.add');
Route::post('/PmbMstPendaftarans/master-unit/store', [MasterBiayaPendaftaranController::class, 'store'])->name('master.unit.store');
Route::delete('/PmbMstPendaftarans/master-unit/delete/{id}', [MasterBiayaPendaftaranController::class, 'destroy'])->name('master.unit.delete');
Route::get('/PmbMstPendaftarans/master-unit/edit/{id}', [MasterBiayaPendaftaranController::class, 'edit'])->name('master.unit.edit');
Route::put('/PmbMstPendaftarans/master-unit/update/{id}', [MasterBiayaPendaftaranController::class, 'update'])->name('master.unit.update');

//master sekolah/prodi
Route::get('/PmbMstPendaftarans/master-sekolah', [MasterProdiController::class, 'index'])->name('master.sekolah');
Route::get('/PmbMstPendaftarans/master-sekolahh/add', [MasterProdiController::class, 'create'])->name('master.sekolah.add2');
Route::post('/PmbMstPendaftarans/master-sekolah/store', [MasterProdiController::class, 'store'])->name('master.sekolah.store');
Route::delete('/PmbMstPendaftarans/master-sekolah/delete/{id}', [MasterProdiController::class, 'destroy'])->name('master.sekolah.delete');
Route::get('/PmbMstPendaftarans/master-sekolah/edit/{id}', [MasterProdiController::class, 'edit'])->name('master.sekolah.edit');
Route::put('/PmbMstPendaftarans/master-sekolah/update/{id}', [MasterProdiController::class, 'update'])->name('master.sekolah.update');

Route::get('PmbMstPendaftarans/master-ujian', [MasterUjianController::class, 'index'])->name('master.ujian');
Route::get('PmbMstPendaftarans/master-ujian/add', [MasterUjianController::class, 'create'])->name('master.ujian.add');
Route::post('PmbMstPendaftarans/master-ujian/store', [MasterUjianController::class, 'store'])->name('master.ujian.store');
Route::get('PmbMstPendaftarans/master-ujian/{id}/edit', [MasterUjianController::class, 'edit'])->name('master.ujian.edit');
Route::put('PmbMstPendaftarans/master-ujian/{id}', [MasterUjianController::class, 'update'])->name('master.ujian.update');
Route::delete('PmbMstPendaftarans/master-ujian/{id}', [MasterUjianController::class, 'destroy'])->name('master.ujian.delete');

// master tahun akademik
Route::get('/PmbMstPendaftarans/master-tahun-akademik', [MasterAkademikController::class, 'index'])->name('master.tahun_akademik');
Route::get('/PmbMstPendaftarans/master-tahun-akademik/add', [MasterAkademikController::class, 'create'])->name('master.tahun_akademik.add');
Route::post('/PmbMstPendaftarans/master-tahun-akademik/store', [MasterAkademikController::class, 'store'])->name('master.tahun_akademik.store');
Route::delete('/PmbMstPendaftarans/master-tahun-akademik/delete/{id}', [MasterAkademikController::class, 'destroy'])->name('master.tahun_akademik.delete');

Route::get('/PmbMstPendaftarans/master-gelombang', [MasterGelombangController::class, 'index'])->name('master.gelombang');
Route::get('/PmbMstPendaftarans/master-gelombang/add', [MasterGelombangController::class, 'create'])->name('master.gelombang.add');
Route::post('/PmbMstPendaftarans/master-gelombang/store', [MasterGelombangController::class, 'store'])->name('master.gelombang.store');
Route::delete('/PmbMstPendaftarans/master-gelombang/delete/{id}', [MasterGelombangController::class, 'destroy'])->name('master.gelombang.delete');
Route::get('PmbMstPendaftarans/master-gelombang/{id}/edit', [MasterGelombangController::class, 'edit'])->name('master.gelombang.edit');
Route::put('PmbMstPendaftarans/master-gelombang/{id}', [MasterGelombangController::class, 'update'])->name('master.gelombang.update');

Route::get('/PmbMstPendaftarans/master-jalur', [MasterJalurController::class, 'index'])->name('master.jalur');
Route::get('/PmbMstPendaftarans/master-jalur/add', [MasterJalurController::class, 'create'])->name('master.jalur.add');
Route::post('/PmbMstPendaftarans/master-jalur/store', [MasterJalurController::class, 'store'])->name('master.jalur.store');
Route::get('/PmbMstPendaftarans/master-jalur/edit/{id}', [MasterJalurController::class, 'edit'])->name('master.jalur.edit');
Route::put('/PmbMstPendaftarans/master-jalur/update/{id}', [MasterJalurController::class, 'update'])->name('master.jalur.update');
Route::delete('/PmbMstPendaftarans/master-jalur/delete/{id}', [MasterJalurController::class, 'destroy'])->name('master.jalur.delete');


       Route::get('PmbMstPendaftarans/cek_berkas_set_ujian', [UjianController::class, 'cekBerkasSetUjian'])
        ->name('ujian.cekBerkasSetUjian');

    Route::get('PmbMstPendaftarans/cetak_kartu_ujian_reguler', [UjianController::class, 'cetakKartuUjian'])
        ->name('ujian.cetakKartuUjianReguler');

       Route::get('/PmbMstPendaftarans/proses_kelulusan/{status}', [UjianController::class, 'setKelulusanPeserta'])
    ->name('ujian.setKelulusan');


    Route::get('PmbMstPendaftarans/edit_jadwal_ujian', [UjianController::class, 'editJadwalUjian'])
        ->name('ujian.editJadwalUjian');
Route::post('/PmbMstPendaftarans/update_jadwal_ujian', [UjianController::class, 'updateJadwalUjian'])
    ->name('pmb.updateJadwalUjian');

    Route::get('PmbMstPendaftarans/set_kelulusan-peserta', [UjianController::class, 'setKelulusan'])
    ->name('pmb.set_kelulusan');



    // Settings

    Route::get('PmbMstPendaftarans/setting-gelombang', [MasterGelombangController::class, 'index2']);
Route::get('PmbMstPendaftarans/setting-harga', [MasterHargaController::class, 'index']);
Route::get('PmbMstPendaftarans/setting-pendaftaran', [MasterBiayaPendaftaranController::class, 'index2']);
Route::resource('PmbMstPendaftarans/setting-slider',ImagesliderController::class);
Route::resource('PmbMstPendaftarans/setting-informasi', InformasiController::class)
    ->names('informasi');
Route::resource('PmbMstPendaftarans/setting-brosur', BrosurController::class);


    Route::post('master_akademik/toggle/{id}', [SettingsController::class, 'toggleAkademik'])->name('master_akademik.toggle');
    Route::post('gelombang/toggle/{id}', [SettingsController::class, 'toggleGelombang'])->name('gelombang.toggle');
   
    Route::get('/PmbMstPendaftarans/registrasi-lunas', [RegisterController::class, 'registrasiLunas'])
     ->name('registrasi.lunas');

     Route::get('PmbMstPendaftarans/registrasi-cekstatus', [RegisterController::class, 'cekStatusIndex'])->name('registrasi.cekstatus.index');
Route::post('PmbMstPendaftarans/registrasi-cekstatus', [RegisterController::class, 'cekStatus'])->name('registrasi.cekStatus');


    // Master Jalur
    Route::get('PmbRefJenispendaftarans/add', [MasterJalurController::class, 'create']);
    Route::post('PmbRefJenispendaftarans/store', [MasterJalurController::class, 'store']);
    Route::get('PmbRefJenispendaftarans/edit/{id}', [MasterJalurController::class, 'edit']);
    Route::put('PmbRefJenispendaftarans/{id}', [MasterJalurController::class, 'update']);

    // Master Biaya Pendaftaran
    Route::get('PmbRefFaculties/edit_nom/{id}', [MasterBiayaPendaftaranController::class, 'edit']);
    Route::put('PmbRefFaculties/edit_nom/{id}', [MasterBiayaPendaftaranController::class, 'update']);

    // Master Prodi
    Route::get('PmbRefProdis/edit/{id}', [MasterProdiController::class, 'edit']);
    Route::put('PmbRefProdis/update/{id}', [MasterProdiController::class, 'update'])->name('pmbrefprodis.update');

    // Bayar
    Route::get('PmbRefPembayarans/add', [BayarController::class, 'create'])->name('bayar.create');
    Route::post('PmbRefPembayarans/store', [BayarController::class, 'store'])->name('bayar.store');

    // Master Harga
    Route::get('/PmbRefMasterHargaPendaftarans/add', [MasterHargaController::class, 'create'])->name('master-harga.create');
    Route::post('/PmbRefMasterHargaPendaftarans/add', [MasterHargaController::class, 'store'])->name('master-harga.store');
    Route::delete('master-harga/destroy/{id}', [MasterHargaController::class, 'destroy'])->name('master-harga.destroy');
Route::get('PmbRefMasterHargaPendaftarans/edit/{id}', [MasterHargaController::class, 'edit'])->name('master-harga.edit');
Route::put('PmbRefMasterHargaPendaftarans/update/{id}', [MasterHargaController::class, 'update'])->name('master-harga.update');

    Route::get('/PmbMstPendaftarans/cek_berkas_pembayaran', [PembayaranController::class, 'cekBerkas'])
        ->name('pembayaran.cekBerkas');
        



});

/*
|--------------------------------------------------------------------------
| API Lokasi Routes
|--------------------------------------------------------------------------
*/
Route::get('/api/provinsi', [LokasiController::class, 'provinsi'])->name('api.provinsi');
Route::get('/api/provinsi/{provinsi}/kabupaten', [LokasiController::class, 'kabupaten'])->name('api.kabupaten');
Route::get('/api/kabupaten/{kota}/kecamatan', [LokasiController::class, 'kecamatan'])->name('api.kecamatan');

Route::get('get-gelombang-by-jalur/{id_jalur}', [UserController::class, 'getGelombangByJalur']);
Route::get('get-fakultas-by-jalur-gelombang/{id_jalur}/{id_gelombang}', [UserController::class, 'getFakultasByJalurGelombang']);
Route::get('get-prodi-by-jalur-gelombang-fakultas/{id_jalur}/{id_gelombang}/{id_fakultas}', [UserController::class, 'getProdiByJalurGelombangFakultas']);
