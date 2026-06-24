# Website Tim Kreatif PMB UAD — Implementation Plan

Website profil & CMS untuk Tim Kreatif Penerimaan Mahasiswa Baru (PMB) Universitas Ahmad Dahlan. Dibangun dengan **Laravel 11**, **MySQL**, **Blade + Tailwind CSS (CDN)**, **Alpine.js (CDN)**, dan **Dompdf**.

---

## User Review Required

> [!IMPORTANT]
> **Versi Laravel**: Saya akan menggunakan **Laravel 11** (latest stable). Apakah ada preferensi versi tertentu?

> [!IMPORTANT]
> **Versi PHP**: Laravel 11 membutuhkan **PHP ≥ 8.2**. Pastikan server hosting mendukung versi ini.

> [!WARNING]
> **Rich Text Editor**: Spesifikasi menyebut CKEditor/Quill. Saya akan menggunakan **Quill.js (via CDN)** karena gratis, ringan, dan mudah diintegrasikan dengan Blade. Apakah ada preferensi lain?

> [!IMPORTANT]
> **Autentikasi Admin**: Saya akan membuat sistem auth sederhana (bukan multi-user) — satu akun admin saja dengan username & password. Jika dibutuhkan multi-admin, harap konfirmasi.

## Open Questions

1. **Logo UAD / Tim Kreatif**: Apakah ada file logo yang perlu diintegrasikan? Atau cukup menggunakan teks saja?
2. **Nama Divisi Awal**: Apakah ada daftar divisi awal yang sudah ditentukan (misal: Desain, Videografi, Konten, dll)?
3. **Periode Default**: Tahun kepengurusan pertama yang akan diinput? (misal: 2024/2025 atau 2025/2026?)
4. **Sosial Media Tim**: Apakah ada akun IG/TikTok resmi tim yang link-nya perlu ditampilkan di footer?

---

## Proposed Changes

### 🗄️ Database Schema (Migration Files)

Semua tabel dan relasi yang dibutuhkan:

#### [NEW] database/migrations/xxxx_create_divisions_table.php
```
divisions
├── id (bigint, PK)
├── name (string) — Nama divisi
├── color_hex (string, 7 chars) — Kode warna hex (#RRGGBB)
├── timestamps
```

#### [NEW] database/migrations/xxxx_create_periods_table.php
```
periods
├── id (bigint, PK)
├── name (string) — Contoh: "2024/2025"
├── is_active (boolean, default: false) — Periode aktif saat ini
├── timestamps
```

#### [NEW] database/migrations/xxxx_create_members_table.php
```
members
├── id (bigint, PK)
├── period_id (FK → periods)
├── division_id (FK → divisions)
├── full_name (string)
├── nickname (string, nullable)
├── photo (string, nullable) — Path foto
├── role (string, nullable) — Jabatan/peran
├── linkedin (string, nullable)
├── instagram (string, nullable)
├── github (string, nullable)
├── timestamps
```

#### [NEW] database/migrations/xxxx_create_events_table.php
```
events
├── id (bigint, PK)
├── title (string) — Nama kegiatan
├── event_date (date)
├── event_time (time, nullable)
├── external_link (string, nullable) — Link IG/YouTube
├── timestamps
```

#### [NEW] database/migrations/xxxx_create_event_member_table.php (pivot)
```
event_member
├── id (bigint, PK)
├── event_id (FK → events)
├── member_id (FK → members)
├── division_id (FK → divisions) — Divisi saat event (bisa berbeda dari divisi utama)
├── timestamps
```

#### [NEW] database/migrations/xxxx_create_news_table.php
```
news
├── id (bigint, PK)
├── title (string)
├── slug (string, unique)
├── banner_image (string, nullable)
├── content (longText) — HTML dari Quill editor
├── published_date (date)
├── timestamps
```

#### [NEW] database/migrations/xxxx_create_news_member_table.php (pivot)
```
news_member
├── id (bigint, PK)
├── news_id (FK → news)
├── member_id (FK → members)
├── timestamps
```

#### [NEW] database/migrations/xxxx_create_settings_table.php
```
settings
├── id (bigint, PK)
├── key (string, unique)
├── value (text, nullable)
├── timestamps
```
> Digunakan untuk: `team_photo`, `division_recap_{id}`, `registrant_count`, `ig_followers`, `ig_reach`, `ig_engagement`, `tiktok_followers`, `tiktok_views`, `tiktok_engagement`, `social_media_display` (ig/tiktok/both), dll.

#### [NEW] database/migrations/xxxx_create_users_table.php
```
users (untuk admin auth)
├── id (bigint, PK)
├── username (string, unique)
├── password (string, hashed)
├── timestamps
```

---

### 🏗️ Models & Relationships

#### [NEW] app/Models/Division.php
- `hasMany(Member::class)`
- `hasMany(EventMember::class)`

#### [NEW] app/Models/Period.php
- `hasMany(Member::class)`
- Scope: `active()`

#### [NEW] app/Models/Member.php
- `belongsTo(Period::class)`
- `belongsTo(Division::class)`
- `belongsToMany(Event::class)` via `event_member`
- `belongsToMany(News::class)` via `news_member`
- Computed: `total_events`, `total_works`
- Accessor: `slug` (nama URL-friendly untuk portfolio)

#### [NEW] app/Models/Event.php
- `belongsToMany(Member::class)` via `event_member` (with pivot: `division_id`)

#### [NEW] app/Models/News.php
- `belongsToMany(Member::class)` via `news_member`

#### [NEW] app/Models/Setting.php
- Static helpers: `get($key)`, `set($key, $value)`

#### [NEW] app/Models/User.php
- Auth model standar Laravel

---

### 🛣️ Routes

#### [NEW] routes/web.php

**Public Routes:**
```
GET  /                          → DashboardController@index         (Dashboard)
GET  /tentang-kami              → AboutController@index             (Tentang Kami)
GET  /portofolio                → PortfolioController@index         (Search/List)
GET  /portofolio?nama={slug}    → PortfolioController@show          (Personal Portfolio)
GET  /portofolio/{slug}/pdf     → PortfolioController@exportPdf     (Export PDF)
GET  /berita/{slug}             → NewsController@show               (Detail Berita)
GET  /login                     → AuthController@showLogin
POST /login                     → AuthController@login
POST /logout                    → AuthController@logout

GET  /api/events/{year}/{month} → EventApiController@getMonthEvents (API kalender)
```

**Admin Routes (middleware: auth):**
```
GET    /admin                          → Admin\DashboardController@index
                                      
# Anggota
GET    /admin/members                  → Admin\MemberController@index
GET    /admin/members/create           → Admin\MemberController@create
POST   /admin/members                  → Admin\MemberController@store
GET    /admin/members/{id}/edit        → Admin\MemberController@edit
PUT    /admin/members/{id}             → Admin\MemberController@update
DELETE /admin/members/{id}             → Admin\MemberController@destroy

# Periode
Resource route → Admin\PeriodController

# Divisi  
Resource route → Admin\DivisionController

# Event/Kalender
Resource route → Admin\EventController

# Berita
Resource route → Admin\NewsController

# Settings (foto bersama, recap divisi, statistik sosmed, jumlah pendaftar)
GET  /admin/settings                   → Admin\SettingController@index
POST /admin/settings                   → Admin\SettingController@update

# Akun
GET  /admin/account                    → Admin\AccountController@edit
PUT  /admin/account                    → Admin\AccountController@update
```

---

### 🎨 Frontend — Public Pages

#### [NEW] resources/views/layouts/app.blade.php
Layout utama publik:
- Navbar: Logo/Nama + navigasi (Dashboard, Tentang Kami, Portofolio, Login Admin)
- Footer: copyright & sosial media tim
- Tailwind CSS via CDN, Alpine.js via CDN
- Google Fonts: Inter
- Tema dominan biru (`#1E40AF` primary, `#3B82F6` secondary, `#DBEAFE` light)
- Glassmorphism effects, smooth transitions, dark-blue gradients

#### [NEW] resources/views/pages/dashboard.blade.php
1. **Hero Section** — Foto bersama full-width dengan overlay gradient biru
2. **Statistik Section** — Cards animasi: Jumlah Pendaftar UAD, Total Anggota, Total Kegiatan
3. **Recap Divisi** — Grid cards per divisi dengan warna indikator
4. **Performa Sosial Media** — Cards IG/TikTok (followers, reach, engagement) dengan toggle pilihan
5. **Kalender Interaktif** — Calendar grid (Alpine.js) → klik tanggal → modal detail event + legenda divisi
6. **Berita Terkini** — Horizontal scroll cards dengan gambar, judul, tanggal, tombol "Baca Selengkapnya"

#### [NEW] resources/views/pages/about.blade.php
1. **Filter Periode** — Dropdown/tabs tahun kepengurusan (Alpine.js toggle)
2. **Grid Anggota** — Cards responsif: foto, nama, divisi (badge warna), peran

#### [NEW] resources/views/pages/portfolio-search.blade.php
- Search bar pencarian nama
- Grid preview anggota (quick links)

#### [NEW] resources/views/pages/portfolio-show.blade.php
1. **Hero** — Foto profil besar, nama lengkap & panggilan, peran, link sosmed, badge kontribusi
2. **Stat Cards** — Total kegiatan, total karya/postingan
3. **Timeline Kegiatan** — Daftar event yang diikuti
4. **Karya/Konten** — Daftar berita/konten yang dibuat
5. **Tombol** — "Cetak Portofolio (PDF)" + "Salin Link" (copy to clipboard)

#### [NEW] resources/views/pages/portfolio-pdf.blade.php
- Layout khusus PDF (Dompdf) — clean, printable, tanpa interaktivitas

#### [NEW] resources/views/pages/news-detail.blade.php
- Banner image, judul, tanggal, konten HTML (dari Quill), credit penulis/pembuat

#### [NEW] resources/views/pages/login.blade.php
- Form login minimalis: username, password, tombol masuk

---

### 🔐 Frontend — Admin Pages

#### [NEW] resources/views/layouts/admin.blade.php
Layout admin:
- Sidebar navigasi: Dashboard, Anggota, Periode, Divisi, Kalender, Berita, Pengaturan, Akun
- Top bar: username + logout
- Tema konsisten (biru), tapi lebih clean/fungsional

#### [NEW] resources/views/admin/dashboard.blade.php
- Overview cards: total anggota, events bulan ini, berita, periode aktif

#### [NEW] resources/views/admin/members/ (index, create, edit)
- Tabel daftar anggota (filterable by period/division)
- Form: nama, panggilan, foto upload, divisi (dropdown), peran, sosmed links
- Preview foto sebelum upload

#### [NEW] resources/views/admin/periods/ (index, create, edit)
- Tabel periode, toggle aktif/non-aktif

#### [NEW] resources/views/admin/divisions/ (index, create, edit)
- Tabel divisi + color picker untuk hex warna
- Preview warna realtime

#### [NEW] resources/views/admin/events/ (index, create, edit)
- Form: judul, tanggal, jam, link eksternal
- **Tag petugas**: Multi-select anggota + pilih divisi per assignment
- Preview kalender mini

#### [NEW] resources/views/admin/news/ (index, create, edit)
- Form: judul, banner upload, tanggal
- **Quill.js editor** untuk konten artikel
- Tag pembuat konten (multi-select anggota)

#### [NEW] resources/views/admin/settings.blade.php
- Upload foto bersama
- Form rekap per divisi (textarea per divisi)
- Input jumlah pendaftar UAD
- Input statistik sosmed IG & TikTok
- Pilih tampilan sosmed (IG/TikTok/Keduanya)

#### [NEW] resources/views/admin/account.blade.php
- Form ubah username & password

---

### ⚙️ Controllers

#### Public Controllers:
| File | Responsibility |
|------|---------------|
| [NEW] `app/Http/Controllers/DashboardController.php` | Load semua data dashboard: settings, recap, events, news |
| [NEW] `app/Http/Controllers/AboutController.php` | Load members by period |
| [NEW] `app/Http/Controllers/PortfolioController.php` | Search, show portfolio, export PDF (Dompdf) |
| [NEW] `app/Http/Controllers/NewsController.php` | Show detail berita |
| [NEW] `app/Http/Controllers/AuthController.php` | Login/logout |
| [NEW] `app/Http/Controllers/EventApiController.php` | JSON API untuk data kalender per bulan |

#### Admin Controllers:
| File | Responsibility |
|------|---------------|
| [NEW] `app/Http/Controllers/Admin/DashboardController.php` | Admin overview |
| [NEW] `app/Http/Controllers/Admin/MemberController.php` | CRUD anggota |
| [NEW] `app/Http/Controllers/Admin/PeriodController.php` | CRUD periode |
| [NEW] `app/Http/Controllers/Admin/DivisionController.php` | CRUD divisi |
| [NEW] `app/Http/Controllers/Admin/EventController.php` | CRUD event + tagging petugas |
| [NEW] `app/Http/Controllers/Admin/NewsController.php` | CRUD berita + rich text + tag pembuat |
| [NEW] `app/Http/Controllers/Admin/SettingController.php` | Update settings (foto, recap, sosmed) |
| [NEW] `app/Http/Controllers/Admin/AccountController.php` | Update kredensial admin |

---

### 📦 Dependencies & Config

#### [NEW] composer.json additions
```
barryvdh/laravel-dompdf — PDF export
```

#### [NEW] database/seeders/AdminSeeder.php
- Seed admin default: username `admin`, password `password` (hashed)

#### [NEW] database/seeders/DivisionSeeder.php
- Seed divisi contoh: Desain (#3B82F6), Videografi (#EF4444), Konten (#10B981), Humas (#F59E0B)

---

### 📁 Struktur Folder Asset

```
public/
├── uploads/
│   ├── team/              — Foto bersama
│   ├── members/           — Foto anggota
│   └── news/              — Banner berita
├── css/
│   └── app.css            — Custom CSS (animasi, glassmorphism, overrides)
└── js/
    └── app.js             — Custom JS (calendar, clipboard, etc.)
```

---

## Verification Plan

### Automated Tests
```bash
php artisan migrate --seed         # Verifikasi migrasi & seeder
php artisan route:list             # Verifikasi semua route terdaftar
php artisan serve                  # Jalankan dev server
```

### Manual Verification
1. **Dashboard** — Cek foto bersama, recap divisi, kalender interaktif, berita cards
2. **Tentang Kami** — Filter periode, grid anggota
3. **Portofolio** — Search nama, generate halaman personal, export PDF, salin link
4. **Admin Login** — Login dengan admin/password
5. **Admin CRUD** — Test semua operasi CRUD untuk anggota, event, berita, divisi, periode
6. **Kalender Modal** — Klik tanggal → pop-up detail dengan legenda warna divisi
7. **Responsive** — Test di mobile, tablet, desktop
8. **PDF Export** — Download PDF portofolio anggota

---

## Estimasi Struktur File

```
website_profile_tim_kreatif_pmb_uad/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   │   ├── AccountController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DivisionController.php
│   │   │   ├── EventController.php
│   │   │   ├── MemberController.php
│   │   │   ├── NewsController.php
│   │   │   ├── PeriodController.php
│   │   │   └── SettingController.php
│   │   ├── AboutController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── EventApiController.php
│   │   ├── NewsController.php
│   │   └── PortfolioController.php
│   └── Models/
│       ├── Division.php
│       ├── Event.php
│       ├── Member.php
│       ├── News.php
│       ├── Period.php
│       ├── Setting.php
│       └── User.php
├── database/
│   ├── migrations/ (8 files)
│   └── seeders/
│       ├── AdminSeeder.php
│       └── DivisionSeeder.php
├── resources/views/
│   ├── admin/
│   │   ├── account.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── divisions/ (index, create, edit)
│   │   ├── events/ (index, create, edit)
│   │   ├── members/ (index, create, edit)
│   │   ├── news/ (index, create, edit)
│   │   ├── periods/ (index, create, edit)
│   │   └── settings.blade.php
│   ├── layouts/
│   │   ├── admin.blade.php
│   │   └── app.blade.php
│   └── pages/
│       ├── dashboard.blade.php
│       ├── about.blade.php
│       ├── login.blade.php
│       ├── news-detail.blade.php
│       ├── portfolio-pdf.blade.php
│       ├── portfolio-search.blade.php
│       └── portfolio-show.blade.php
├── public/
│   ├── css/app.css
│   ├── js/app.js
│   └── uploads/ (team/, members/, news/)
└── routes/web.php
```

> Total file baru: **~50+ files** (migrations, models, controllers, views, assets)
