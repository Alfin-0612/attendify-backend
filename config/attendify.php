<?php

return [
    // Tanggal Senin pekan pertama semester (pekan ke-1). 26 Okt 2026 = pekan ke-6.
    'semester_mulai' => env('SEMESTER_MULAI', '2026-09-21'),
    'semester_label' => env('SEMESTER_LABEL', 'Semester Ganjil T.A. 2026/2027'),

    // 'stub'    = pengenalan wajah palsu (selalu cocok), hanya untuk pengembangan.
    // 'service' = panggil layanan Python lewat HTTP (FACE_SERVICE_URL).
    'face_mode' => env('FACE_VERIFY_MODE', 'stub'),
    'face_service_url' => env('FACE_SERVICE_URL'),
];
