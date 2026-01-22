<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // ✅ PERBAIKAN: Gunakan '*' agar SEMUA URL terkena whitelist CORS
    // Ini membantu mengatasi masalah jika browser mengakses path yang tidak terduga
    'paths' => ['*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Kadang browser butuh ini true, tapi jika allowed_origins adalah '*', 
    // ini harus false. Jika kamu spesifik menyebutkan http://localhost:3000, ini boleh true.
    // Untuk amannya biarkan false dengan origin '*'.
    'supports_credentials' => false,

];