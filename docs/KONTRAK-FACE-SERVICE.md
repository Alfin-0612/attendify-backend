# Kontrak Layanan Pengenalan Wajah (Attendify)

Dokumen untuk tim Sains Data. Layanan ini dipanggil oleh backend Laravel setiap kali
siswa melakukan presensi. Aplikasi mobile **tidak** memanggil layanan ini langsung.

## Alur

```
Flutter ──foto──▶ Laravel (POST /api/presensi) ──foto + foto referensi──▶ Layanan Wajah
                        ▲                                                       │
                        └──────────────── JSON { cocok, skor, alasan } ─────────┘
```

Laravel yang menyimpan hasil presensi dan mengatur jendela waktu, sesi, serta duplikasi.
Layanan wajah **hanya memutuskan apakah wajah pada foto sama dengan foto referensi siswa**.

## Request

`POST {FACE_SERVICE_URL}` dengan `multipart/form-data`

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `foto` | file (JPEG/PNG) | ya | Foto yang baru diambil dari kamera depan HP |
| `foto_referensi` | file (JPEG/PNG) | tidak | Foto pendaftaran siswa. Tidak ada bila admin belum mengunggahnya |
| `nisn` | string | ya | Identitas siswa |
| `nama` | string | ya | Nama siswa (untuk log) |

Catatan: foto dari kamera depan Android biasanya **sudah dalam orientasi sesuai EXIF**;
mohon baca EXIF atau uji dengan foto asli dari aplikasi.

## Response

Status `200` dengan JSON:

```json
{ "cocok": true, "skor": 0.93, "alasan": null }
```

| Field | Tipe | Keterangan |
|---|---|---|
| `cocok` | boolean | Hasil keputusan akhir. Ambang batas ditentukan di sisi layanan |
| `skor` | number 0–1 | Tingkat kemiripan (disimpan Laravel untuk audit) |
| `alasan` | string/null | Wajib diisi bila `cocok` = false. Nilai yang dikenali: |

| `alasan` | Pesan yang tampil ke siswa |
|---|---|
| `wajah_tidak_cocok` | Wajah tidak dikenali |
| `wajah_tidak_terdeteksi` | Wajah tidak terdeteksi, posisikan di dalam oval |
| `pencahayaan_buruk` | Cahaya kurang atau terlalu silau |
| `wajah_tertutup` | Lepas masker atau topi |
| `liveness_gagal` | Terindikasi foto/layar, bukan wajah asli |
| `foto_referensi_belum_ada` | Wajah belum didaftarkan |

Status selain `200` atau timeout (20 detik) diperlakukan Laravel sebagai
"layanan tidak tersedia" dan presensi **tidak** dicatat.

## Yang diharapkan dari layanan

- Respons rata-rata ≤ 3 detik per permintaan.
- **Liveness / anti-spoofing** (menolak foto cetak atau layar ponsel) — sangat dianjurkan.
- Tidak menyimpan foto lebih lama dari yang diperlukan (data biometrik, UU PDP).
- Mengutamakan menekan *false accept* (orang lain diterima) dibanding *false reject*.

## Cara menyambungkan

Di file `.env` pada proyek Laravel:

```
FACE_VERIFY_MODE=service
FACE_SERVICE_URL=http://127.0.0.1:8001/verifikasi
```

Lalu jalankan ulang `php artisan serve`. Untuk kembali ke mode palsu (pengembangan),
ubah `FACE_VERIFY_MODE=stub`.

## Uji cepat tanpa aplikasi

```
curl -X POST http://127.0.0.1:8001/verifikasi \
  -F "foto=@foto_baru.jpg" -F "foto_referensi=@foto_daftar.jpg" \
  -F "nisn=5240411198" -F "nama=Ferris Ali"
```
