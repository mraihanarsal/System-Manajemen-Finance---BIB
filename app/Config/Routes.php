<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index');

// ================== AUTH ==================
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

// ================== DASHBOARD ==================
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/profile', 'Dashboard::profile');
$routes->post('/dashboard/update_profile', 'Dashboard::update_profile');
$routes->post('/dashboard/upload_foto', 'Dashboard::upload_foto');
$routes->post('/dashboard/ganti_password', 'Dashboard::ganti_password');
$routes->get('/dashboard/kelola_pengguna', 'Dashboard::kelola_pengguna');
$routes->get('/dashboard/login_logout', 'Dashboard::login_logout');

// ================== USER API ==================
$routes->group('api', function ($routes) {
    $routes->post('users', 'UserApi::create');
    $routes->put('users/(:num)', 'UserApi::update/$1');
    $routes->delete('users/(:num)', 'UserApi::delete/$1');
    $routes->get('users', 'UserApi::getAll');
});

// ================== OTHER ==================
$routes->get('/charts', 'Charts::index');
$routes->get('/tables', 'Tables::index');
$routes->group('pengeluaran', function($routes) {
    $routes->get('/', 'Pengeluaran::index');
    $routes->get('getAll', 'Pengeluaran::getAll');
    $routes->get('getTotal', 'Pengeluaran::getTotal');
    $routes->post('tambah', 'Pengeluaran::tambah');
    $routes->post('ubah/(:num)', 'Pengeluaran::ubah/$1');
    $routes->post('hapus/(:num)', 'Pengeluaran::hapus/$1');
});
$routes->get('/laporan', 'Laporan::index');
$routes->get('/laporan/getData', 'Laporan::getData');

// ================== TIKTOK ==================
$routes->get('/tiktok/toko', 'TokoTiktokController::index');
$routes->post('/tiktok/toko/store', 'TokoTiktokController::store');
$routes->post('/tiktok/toko/update/(:any)', 'TokoTiktokController::update/$1');
$routes->get('/tiktok/toko/deactivate/(:any)', 'TokoTiktokController::deactivate/$1');
$routes->get('/tiktok/toko/activate/(:any)', 'TokoTiktokController::activate/$1');
$routes->get('/tiktok/toko/delete/(:any)', 'TokoTiktokController::delete/$1');

$routes->get('tiktok', 'TiktokController::index');
$routes->get('tiktok/pendapatan', 'TiktokController::pendapatan');
$routes->get('tiktok/detail/(:any)', 'DetailTiktokController::index/$1');
$routes->group('tiktok/transaksi', function ($r) {

    $r->get('pendapatan/(:segment)', 'TiktokTransaksiController::pendapatan/$1');
    $r->get('laba/(:segment)', 'TiktokTransaksiController::laba/$1');
    $r->post('laba/(:segment)', 'TiktokTransaksiController::processLaba/$1');
    $r->get('resetLaba/(:segment)', 'TiktokTransaksiController::resetLaba/$1');
    $r->get('resetPendapatan/(:segment)', 'TiktokTransaksiController::resetPendapatan/$1');
    $r->post('addBarang/(:segment)', 'TiktokTransaksiController::addBarang/$1');
    $r->get('deleteBarang/(:segment)/(:segment)', 'TiktokTransaksiController::deleteBarang/$1/$2');
    $r->get('clearBarang/(:segment)', 'TiktokTransaksiController::clearBarang/$1');
    $r->post('processPendapatan/(:segment)', 'TiktokTransaksiController::processPendapatan/$1');
    $r->get('delete/(:num)', 'TiktokTransaksiController::deleteTransaction/$1');
    $r->get('deleteAllPendapatan/(:segment)', 'TiktokTransaksiController::deleteAllPendapatan/$1');
    $r->get('deleteAllLaba/(:segment)', 'TiktokTransaksiController::deleteAllLaba/$1');
    $r->post('processLaba/(:segment)', 'TiktokTransaksiController::processLaba/$1');
    $r->post('savePendapatan/(:segment)', 'TiktokTransaksiController::savePendapatan/$1');
    $r->post('saveLaba/(:segment)', 'TiktokTransaksiController::saveLaba/$1');
    $r->get('deleteLabaHistory/(:segment)', 'TiktokTransaksiController::deleteLabaHistory/$1');
    $r->get('report/(:segment)', 'TiktokTransaksiController::report/$1');
});

// ================== ZEFATEX ==================
$routes->group('zefatex', function ($r) {
    $r->get('/', 'ZefatexController::index');
    $r->get('create', 'ZefatexController::create');
    $r->post('store', 'ZefatexController::store');
    $r->get('edit/(:num)', 'ZefatexController::edit/$1');
    $r->post('update/(:num)', 'ZefatexController::update/$1');
    $r->get('delete/(:num)', 'ZefatexController::delete/$1');
});

// ================== SHOPEE ==================
$routes->get('shopee', 'ShopeeController::index');
$routes->get('shopee/pendapatan', 'ShopeeController::pendapatan');

// === DATA TOKO SHOPEE ===
$routes->get('shopee/toko', 'TokoShopeeController::index');
$routes->post('shopee/toko/store', 'TokoShopeeController::store');
$routes->get('shopee/toko/edit/(:segment)', 'TokoShopeeController::edit/$1');
$routes->post('shopee/toko/update/(:segment)', 'TokoShopeeController::update/$1');
$routes->get('shopee/toko/delete/(:segment)', 'TokoShopeeController::delete/$1');

// Aktif-Non aktif Shopee
$routes->get('shopee/toko/activate/(:segment)', 'TokoShopeeController::activate/$1');
$routes->get('shopee/toko/deactivate/(:segment)', 'TokoShopeeController::deactivate/$1');

// === DETAIL TOKO ===
$routes->get('shopee/detail/(:segment)', 'DetailShopeeController::index/$1');

// === TRANSAKSI PER TOKO ===
$routes->get('shopee/transaksi/(:segment)', 'UploadReportsShopee::index/$1');

// === UPLOAD PDF ===
$routes->post('shopee/transaksi/upload/(:segment)', 'UploadReportsShopee::upload/$1');

// === HAPUS PDF ===
$routes->get('shopee/transaksi/delete/(:segment)', 'UploadReportsShopee::delete/$1');
// === DEBUG ===
$routes->get('debug/schema', 'DebugController::schema');
$routes->get('debug/fix', 'DebugController::fix_schema');
