# Dokumentasi Teknis — Sistem Blast WA & Email

---

## 1. Overview

Sistem blast WA dan Email berbasis Laravel untuk kebutuhan pengiriman pesan massal. Mendukung dua mode blast — transaksional untuk pengiriman berbasis data spesifik per kontak, dan pemasaran untuk pengiriman promo ke phonebook. Terintegrasi dengan MPWA sebagai gateway WhatsApp dan SMTP Niaga Hoster untuk email.

**Tech Stack**
- Framework: Laravel (PHP)
- WhatsApp Gateway: MPWA (self-hosted)
- Email: SMTP Niaga Hoster
- Queue: Laravel Queue (driver menyesuaikan environment)
- Storage: Laravel Storage (local/public disk)
- Skala target: 1.000–10.000 kontak

---

## 2. Arsitektur Sistem

```
[ Browser / UI ]
       |
[ Laravel App ]
       |
  ┌────┴────┐
  ▼         ▼
[ Queue ]  [ Storage ]
  |              |
  └── BlastService
          |
     ┌────┴────┐
     ▼         ▼
  [MPWA]    [SMTP]
          |
  CampaignRecipient (log)
```

Config MPWA dan SMTP tidak disimpan di `.env` melainkan di tabel `configs` dalam format JSON, sehingga bisa diubah dari UI tanpa menyentuh server.

Queue driver dikonfigurasi via `.env`:
- `sync` untuk lokal
- `database` untuk shared hosting
- `redis` untuk VPS

---

## 3. Database

### ERD

```
configs

contacts ──────────── contact_group_members ──── contact_groups
                                                       |
campaigns ──── campaign_contact_groups ────────────────┘
    |
campaign_recipients
    |
contacts (nullable FK)

campaigns ──── templates (nullable FK)
```

### Penjelasan Tabel

**configs**
Menyimpan konfigurasi sistem dalam format key-value JSON. Key yang dipakai: `whatsapp` dan `smtp`.

Kolom: id, key (unique), value (JSON), timestamps

---

**contacts**
Phonebook terpusat. Semua kontak dari jalur manapun masuk ke tabel ini.

Kolom: id, name, phone (nullable), email (nullable), timestamps

---

**contact_groups**
Nama group kontak yang diisi manual oleh user. Group dibuat otomatis setiap ada upload Excel baru.

Kolom: id, name, timestamps

---

**contact_group_members**
Pivot antara contacts dan contact_groups.

Kolom: id, contact_id (FK), contact_group_id (FK), unique(contact_id, contact_group_id)

---

**templates**
Template pesan yang bisa disimpan dan dipakai ulang lintas campaign.

Kolom: id, name, channel (wa/email), subject (nullable), body, timestamps

---

**campaigns**
Satu record per campaign blast.

Kolom: id, name, type (transactional/marketing), channel (wa/email), template_id (FK nullable), subject (nullable), body, file_path (nullable), image_path (nullable), use_qr (boolean), qr_column (nullable), status (scheduled/processing/completed/failed), scheduled_at, started_at, finished_at, timestamps

---

**campaign_contact_groups**
Pivot antara campaigns dan contact_groups. Menyimpan group mana saja yang dipilih untuk blast pemasaran.

Kolom: id, campaign_id (FK), contact_group_id (FK), unique(campaign_id, contact_group_id)

---

**campaign_recipients**
Log pengiriman per kontak per campaign. Ditulis setelah proses kirim, bukan sebelumnya.

Kolom: id, campaign_id (FK), contact_id (FK nullable), name, phone (nullable), email (nullable), status (sent/failed/skipped), error_message (nullable), sent_at (nullable), timestamps

---

## 4. Flow Sistem

### Blast Transaksional

```
Pilih channel (WA / Email)
        ↓
Upload Excel
        ↓
Form konfirmasi mapping:
  - Nama group (input manual)
  - Kolom nama  → dropdown header Excel
  - Kolom phone → dropdown header Excel
  - Kolom email → dropdown header Excel
        ↓
Kontak tersimpan ke phonebook
File Excel tersimpan ke storage
        ↓
Buat template (variabel dari header Excel)
Opsional: aktifkan QR, pilih kolom value QR
Opsional: upload gambar (khusus WA)
        ↓
Set jadwal → Campaign tersimpan (status: scheduled)
        ↓
Scheduler trigger blast:dispatch setiap menit
        ↓
ProcessBlastJob dispatch
        ↓
BlastService baca file Excel baris per baris
        ↓
Per baris:
  - Cek duplikasi by phone/email
  - Render template dengan variabel dari baris Excel
  - Generate QR jika use_qr aktif (khusus WA)
  - Tentukan mode kirim: QR / gambar / teks biasa
  - Kirim via MPWA atau SMTP
  - Tulis log ke campaign_recipients
  - Delay 1 detik
        ↓
Campaign selesai (status: completed / failed)
```

### Blast Pemasaran

```
Pilih channel (WA / Email)
        ↓
Pilih kontak dari phonebook
(by group / beberapa group / semua)
        ↓
Buat template
Variabel yang tersedia: {{name}}, {{phone}}, {{email}}
Opsional: upload gambar (khusus WA)
        ↓
Set jadwal → Campaign tersimpan (status: scheduled)
        ↓
Scheduler trigger blast:dispatch setiap menit
        ↓
ProcessBlastJob dispatch
        ↓
BlastService baca kontak dari group yang dipilih
        ↓
Per kontak:
  - Cek duplikasi by phone/email
  - Render template dengan variabel kontak
  - Tentukan mode kirim: gambar / teks biasa
  - Kirim via MPWA atau SMTP
  - Tulis log ke campaign_recipients
  - Delay 1 detik
        ↓
Campaign selesai (status: completed / failed)
```

### Import Phonebook Langsung

```
Upload Excel dari menu Phonebook
        ↓
Form konfirmasi mapping:
  - Nama group (input manual)
  - Kolom nama  → dropdown
  - Kolom phone → dropdown
  - Kolom email → dropdown
        ↓
Kontak tersimpan ke phonebook
```

---

## 5. Struktur Folder & File

```
app/
├── Console/
│   └── Commands/
│       └── DispatchScheduledBlasts.php    # Command untuk dispatch campaign yang sudah waktunya
├── Http/
│   └── Controllers/
│       ├── ConfigController.php            # Kelola config MPWA & SMTP
│       ├── ContactController.php           # List & hapus kontak
│       ├── ContactGroupController.php      # CRUD group kontak
│       ├── PhonebookImportController.php   # Upload & import Excel ke phonebook
│       ├── TemplateController.php          # CRUD template
│       ├── CampaignController.php          # Buat & lihat campaign
│       ├── CampaignDispatchController.php  # Dispatch blast job manual
│       └── ExcelHeaderController.php       # Ambil headers dari file Excel
├── Jobs/
│   └── ProcessBlastJob.php                # Job untuk proses blast
├── Models/
│   ├── Config.php
│   ├── Contact.php
│   ├── ContactGroup.php
│   ├── Template.php
│   ├── Campaign.php
│   └── CampaignRecipient.php
└── Services/
    ├── ConfigService.php                   # Ambil & simpan config dari DB
    ├── TemplateService.php                 # Render & extract variabel template
    ├── WhatsappService.php                 # Integrasi MPWA
    ├── EmailService.php                    # Integrasi SMTP
    ├── QrCodeService.php                   # Generate & kelola QR code
    ├── ContactImportService.php            # Baca Excel & import ke phonebook
    └── BlastService.php                    # Koordinasi proses blast

database/
└── migrations/
    ├── create_configs_table.php
    ├── create_contact_groups_table.php
    ├── create_contacts_table.php
    ├── create_contact_group_members_table.php
    ├── create_templates_table.php
    ├── create_campaigns_table.php
    ├── create_campaign_contact_groups_table.php
    └── create_campaign_recipients_table.php

routes/
├── web.php
└── console.php

storage/
└── app/
    └── public/
        ├── blasts/           # File Excel blast transaksional
        ├── campaign-images/  # Gambar yang diupload untuk blast WA
        ├── qr/               # QR code temporary (dihapus setelah terkirim)
        └── temp/             # File Excel temporary untuk baca headers
```

---

## 6. Integrasi Eksternal

### MPWA — Send Message

```
POST {base_url}/send-message

Body:
  api_key  : string (required)
  sender   : string (required) — nomor device MPWA
  number   : string (required) — nomor penerima format 62xxx
  message  : string (required)
```

### MPWA — Send Media

```
POST {base_url}/send-media

Body:
  api_key    : string (required)
  sender     : string (required)
  number     : string (required)
  media_type : string (required) — image/video/audio/document
  url        : string (required) — direct URL media, harus publicly accessible
  caption    : string (optional)
```

### SMTP Niaga Hoster

Konfigurasi di-inject runtime via `Config::set()` dari data tabel configs. Field yang dibutuhkan: host, port, username, password, encryption (tls/ssl), from_email, from_name.

---

## 7. Setup & Konfigurasi

### Config di Database

Setelah setup, isi config via UI atau langsung insert ke tabel configs:

```json
// key: whatsapp
{
  "base_url": "http://your-mpwa-server",
  "api_key": "your-api-key",
  "sender": "628xxxxxxxxxx"
}

// key: smtp
{
  "host": "mail.yourdomain.com",
  "port": 465,
  "username": "your@email.com",
  "password": "your-password",
  "encryption": "ssl",
  "from_email": "your@email.com",
  "from_name": "Nama Pengirim"
}
```

### Queue per Environment

**Shared hosting** — tambah cron job di cPanel:
```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

**VPS** — setup Supervisor:
```ini
[program:blast-worker]
command=php /var/www/html/artisan queue:work --sleep=3 --tries=1 --timeout=3600
autostart=true
autorestart=true
numprocs=1
stdout_logfile=/var/www/html/storage/logs/worker.log
```

**Lokal** — set di `.env`:
```
QUEUE_CONNECTION=sync
```
