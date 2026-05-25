# Panduan Implementasi TTE SiAgen UNY (Alur Penomoran & TTE)

## Deskripsi Tugas
Tugas ini bertujuan untuk mengintegrasikan sistem dengan API SiAgen UNY untuk melakukan proses Tanda Tangan Elektronik (TTE) tersertifikasi. 

**Catatan Penting:**
1. Desain template surat/sertifikat akan **menyimpan dan menampilkan nomor surat** yang didapat dari SiAgen.
2. Alur *generate* surat secara inti **tidak berubah**, hanya tahapannya yang menyesuaikan dengan API dari SiAgen (yaitu: Minta Nomor -> Generate PDF dengan Nomor -> Upload PDF -> Eksekusi TTE).

Silakan ikuti panduan implementasi langkah demi langkah di bawah ini. Gunakan standar Laravel (facade `Http`).

---

## Tahap 1: Persiapan Database
Kita perlu menyimpan data balasan dari SiAgen.
Tambahkan kolom berikut pada tabel yang menyimpan data surat/sertifikat (misal: tabel `certificates`):
- `siagen_id` (string/integer, nullable) -> Untuk menyimpan `id` balasan dari SiAgen.
- `siagen_nomor` (string, nullable) -> Untuk menyimpan `nomor` balasan dari SiAgen.

---

## Tahap 2: Persiapan Environment Variables
Pastikan konfigurasi API SiAgen tersimpan di `.env`.
```env
SIAGEN_API_KEY=Lw_oJ3KQomQnh_eT29Ep9Li3ybDpiPrY
SIAGEN_BASE_URL=https://siagen.uny.ac.id
```

---

## Tahap 3: Pembuatan Service API (`app/Services/SiAgenService.php`)
Buat kelas Service untuk berinteraksi dengan 3 endpoint utama SiAgen.

### 1. Endpoint A: Pengambilan Nomor Surat
- **Endpoint:** `POST {SIAGEN_BASE_URL}/penomoran-rest/create?scheme=nomor`
- **Tipe Request:** `multipart/form-data`
- **Header:** `key: {SIAGEN_API_KEY}`
- **Parameter Form-Data:**
  1. `ttd_id` (Integer): ID pejabat penandatangan (dari endpoint `/jabatan-rest/penandatangan`).
  2. `keamanan_id` (Integer): Klasifikasi keamanan (contoh: 4 untuk "Biasa/Terbuka").
  3. `kodesuratid` (Integer): ID kode surat (cari yang `disabled=false`).
  4. `create_at` (Date `Y-m-d`): Tanggal surat (contoh: `2025-01-01`). Maksimal hari ini.
  5. `hal` (String): Perihal surat.
  6. `jenis_surat_id` (Integer): ID jenis surat.

**Tugas Programmer/AI:**
Buat method `requestNomorSurat(array $data)`. Jika respons `"status": true`, kembalikan array berisi `id` dan `nomor`.

### 2. Endpoint B: Upload File Surat
- **Endpoint:** `POST {SIAGEN_BASE_URL}/penomoran-rest/upload`
- **Tipe Request:** `multipart/form-data`
- **Header:** `key: {SIAGEN_API_KEY}`
- **Parameter Form-Data:**
  1. `nomor_surat` (String): Didapat dari Tahap 1.
  2. `id` (Integer/String): Didapat dari Tahap 1.
  3. `file` (File): File arsip (PDF) yang sudah di-generate.

**Tugas Programmer/AI:**
Buat method `uploadFileSurat($siagenId, $nomorSurat, $filePath)`. Gunakan `Http::asMultipart()->attach('file', fopen($filePath, 'r'), 'dokumen.pdf')`. Pastikan respons `"status": true`.

*(Catatan: Update nomor surat tidak dapat dilakukan. Jika upload ulang ke ID yang sama, file lama akan terganti/hilang jika sudah di-TTE).*

### 3. Endpoint D: Eksekusi TTE pada Nomor Surat
- **Endpoint:** `POST {SIAGEN_BASE_URL}/tte-rest/nomor`
- **Tipe Request:** `multipart/form-data` atau `application/x-www-form-urlencoded`
- **Header:** `key: {SIAGEN_API_KEY}`
- **Parameter Form-Data:**
  1. `id` (Integer/String): Didapat dari Tahap 1.
  2. `nomor_surat` (String): Didapat dari Tahap 1.
  3. `email` (String): Email penandatangan.
  4. `passphrase` (String): Passphrase TTE.
  5. `nik` (String): NIK penandatangan (**HANYA UNTUK DEVELOPMENT**, jangan dikirim di Production).

**Tugas Programmer/AI:**
Buat method `executeTte($siagenId, $nomorSurat, $email, $passphrase, $nik = null)`. 
Implementasikan pengecekan environment:
```php
$payload = [
    'id' => $siagenId,
    'nomor_surat' => $nomorSurat,
    'email' => $email,
    'passphrase' => $passphrase,
];

if (config('app.env') !== 'production' && !empty($nik)) {
    $payload['nik'] = $nik;
}

// Lakukan HTTP POST...
```

---

## Tahap 4: Penyesuaian Alur Generate Surat (Job / Controller)
Alur *generate* surat tidak berubah secara sistemik, namun urutan eksekusinya menyesuaikan kebutuhan nomor surat di dalam template PDF.

**Urutan Eksekusi yang Benar:**

```php
// 1. Minta Nomor Surat ke SiAgen terlebih dahulu
$nomorData = $siagenService->requestNomorSurat([
    // ... isi parameter sesuai ketentuan Tahap 3
]);

if(!$nomorData || $nomorData['status'] !== true) {
    throw new Exception("Gagal mendapatkan nomor surat dari SiAgen");
}

// 2. Simpan nomor ke database
$certificate->update([
    'siagen_id' => $nomorData['id'],
    'siagen_nomor' => $nomorData['nomor']
]);

// 3. Generate PDF Dokumen
// CATATAN: Pastikan template Blade PDF Anda menerima variabel $nomorData['nomor'] 
// agar nomor surat tercetak di dalam desain template PDF.
$pdfPath = $this->generatePdfDocument($certificate, $nomorData['nomor']);

// 4. Upload File PDF ke SiAgen
$uploadStatus = $siagenService->uploadFileSurat($nomorData['id'], $nomorData['nomor'], $pdfPath);

if(!$uploadStatus || $uploadStatus['status'] !== true) {
    throw new Exception("Gagal mengunggah dokumen ke SiAgen");
}

// 5. Eksekusi TTE
$tteStatus = $siagenService->executeTte(
    $nomorData['id'], 
    $nomorData['nomor'], 
    $request->email, 
    $request->passphrase, 
    $request->nik
);

// 6. Selesai
if ($tteStatus && $tteStatus['status'] === true) {
    $certificate->update(['status' => 'completed']);
}
```

### Kesimpulan untuk Junior Programmer:
1. Selalu periksa nilai `"status": true` pada JSON balasan, jangan hanya mengandalkan HTTP status 200.
2. Gunakan `Http::asMultipart()` di Laravel saat mengirim file.
3. Karena desain template memerlukan nomor surat, **WAJIB** hit endpoint penomoran *sebelum* merender view/HTML menjadi PDF.
