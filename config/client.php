<?php

/*
| Identitas client yang tampil di halaman publik, dashboard, dan cetakan.
| Sengaja terpisah dari APP_NAME: APP_NAME dipakai untuk nama cookie login,
| jadi mengubahnya akan me-logout semua user.
*/
return [
    'name' => env('CLIENT_NAME', 'Al Istiqomah'),
    'city' => env('CLIENT_CITY', 'Kuningan'),
    'region' => env('CLIENT_REGION', 'Kab. Kuningan, Prov. Jawa Barat'),
    'logo' => env('CLIENT_LOGO', 'istiqomah.png'),
];
