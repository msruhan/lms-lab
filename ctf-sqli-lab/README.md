# 🏴‍☠️ CTF SQL Injection Lab
### Basic Penetration Testing — Learning Management System

Lab Capture The Flag (CTF) untuk kelas **Basic Penetration Testing** dengan fokus pada kerentanan **SQL Injection**. Lab ini dikemas dalam Docker container berbasis Ubuntu 24.04 LTS.

---

## 🚀 Quick Start

```bash
# Build dan jalankan
docker-compose up -d --build

# Akses
# Web App  → http://localhost:8080
# SSH      → ssh ctfadmin@localhost -p 2222 (pass: dvwa2024!)
# MySQL    → mysql -h 127.0.0.1 -P 33060 -u ctfuser -p (pass: ctfpass123)

# Stop
docker-compose down

# Reset (hapus data)
docker-compose down -v
docker-compose up -d --build
```

## 📦 Struktur Project

```
ctf-sqli-lab/
├── Dockerfile                 # Ubuntu 24.04 + Apache + PHP + MariaDB
├── docker-compose.yml         # Docker orchestration
├── config/
│   ├── apache-site.conf       # Apache virtual host config
│   ├── supervisord.conf       # Process manager
│   └── startup.sh             # Initialization script
├── db/
│   └── init.sql               # Database schema + seed data + flags
├── src/
│   ├── index.php              # Main dashboard
│   ├── robots.txt             # Info disclosure (intentional)
│   ├── phpinfo.php            # PHP info (intentional)
│   ├── config/
│   │   └── database.php       # DB connection
│   ├── includes/
│   │   ├── header.php         # Shared header/nav
│   │   └── footer.php         # Shared footer
│   └── challenges/
│       ├── login.php          # Ch1: Auth Bypass
│       ├── products.php       # Ch2: UNION-based SQLi
│       ├── employees.php      # Ch3: Blind SQLi (Boolean)
│       ├── articles.php       # Ch4: Error-based SQLi
│       ├── search_advanced.php # Ch5: Multi-point injection
│       ├── feedback.php       # Ch6: Time-based Blind SQLi
│       └── logout.php
└── docs/
    └── CTF_GUIDE_QA.md        # Panduan lengkap + Soal & Jawaban
```

## 🎯 Challenges

| # | Challenge | Difficulty | Technique | Points |
|---|-----------|------------|-----------|--------|
| 1 | Login Bypass | 🟢 Easy | Authentication Bypass | 100 |
| 2 | Product Catalog | 🟡 Medium | UNION-Based SQLi | 150 |
| 3 | Employee Directory | 🟡 Medium | Blind SQLi (Boolean) | 150 |
| 4 | News Portal | 🟢 Easy | Error-Based SQLi | 100 |
| 5 | Advanced Search | 🔴 Hard | Multi-Point / LOAD_FILE | 200 |
| 6 | Feedback Form | 🔴 Hard | Time-Based Blind SQLi | 200 |

## 🚩 Flag Format

Semua flag mengikuti format: `FLAG{...}`

Total: **8+ flags** tersebar di database dan filesystem.

## 📖 Dokumentasi

Panduan lengkap termasuk soal, jawaban, payload, dan rubrik penilaian tersedia di:
- `docs/CTF_GUIDE_QA.md`

## ⚙️ Tech Stack

- **OS:** Ubuntu 24.04 LTS
- **Web Server:** Apache 2.4
- **Backend:** PHP 8.x
- **Database:** MariaDB 10.x
- **Process Manager:** Supervisor
- **Container:** Docker + Docker Compose

---

## 🔗 Integrasi ke LMS

### Cara Cepat (Otomatis)

```bash
# Dari folder ctf-sqli-lab:
chmod +x build-and-register.sh
./build-and-register.sh
```

Script ini akan:
1. Build Docker image `lms/ctf-sqli-lab:v2`
2. Jalankan seeder Laravel untuk mendaftarkan machine ke database
3. Aktifkan machine secara otomatis

### Cara Manual

**Step 1 — Build image:**
```bash
cd LMS-Lab/ctf-sqli-lab
docker build -t lms/ctf-sqli-lab:v2 .
```

**Step 2 — Daftarkan ke database:**
```bash
cd LMS-Backend
php artisan db:seed --class=LabMachineSeeder
```

**Step 3 — Aktifkan di Admin Panel:**
1. Buka **Admin → Lab Machines**
2. Cari **"CTF SQLi Lab v2"** → klik toggle untuk aktifkan
3. Klik tombol **"Build"** (ikon petir ⚡) untuk memverifikasi image sudah tersedia

**Step 4 — Hubungkan ke Room:**
1. Buka **Admin → Rooms** → edit room Basic Penetration Testing
2. Set **Lab Machine** = `CTF SQLi Lab v2`
3. Pada task yang relevan, centang **"Tampilkan tombol Start Machine"**

### Akses Lab

Setelah student klik "Start Machine" di room:
- Target container berjalan di Docker network internal
- Student mendapat IP target (mis. `172.19.0.3`) dan port `80`
- Dari Kali Linux (attacker), akses: `http://172.19.0.3/`

### Build via Admin Panel

Jika image belum di-build, admin bisa build langsung dari panel:
1. **Admin → Lab Machines** → cari CTF SQLi Lab v2
2. Klik tombol **⚡ Build** (warna kuning)
3. Modal progress akan muncul — tunggu hingga selesai (~3-10 menit)

---

## ⚠️ Disclaimer

Lab ini berisi **aplikasi web yang sengaja dibuat rentan** untuk tujuan edukasi. 
**JANGAN** deploy di environment production atau jaringan publik.

---

*Created for LMS — Basic Penetration Testing Course*
