# THM Lite Desktop (Google Only)

Contoh lab desktop ringan ala TryHackMe untuk LMS:

- Base image: `kasmweb/kali-rolling-desktop:1.14.0`
- noVNC/KasmVNC tetap berjalan (HD desktop di browser)
- Desktop disederhanakan (fokus Firefox + Terminal)
- Firefox diberi policy website filter
- Website non-Google diblok di level browser

## Build Cepat

```bash
cd LMS-Lab/thm-lite-desktop
docker build -t lms/thm-lite-desktop:v1 .
```

## Register ke LMS (Preset)

Seeder sudah ditambahkan ke `LabMachineSeeder`:

- Name: `THM Lite Desktop (Google Only)`
- Image: `lms/thm-lite-desktop:v1`
- Status default: nonaktif sampai image dibuild

Jalankan:

```bash
cd LMS-Backend
php artisan db:seed --class=LabMachineSeeder --force
```

Lalu di Admin Panel:

1. Buka `Admin -> Lab Machines`
2. Cari machine `THM Lite Desktop (Google Only)`
3. Klik `Build` atau `Rebuild`
4. Setelah status `ready`, machine bisa dipilih di room/challenge seperti flow biasa

## Validasi Perilaku

Di desktop lab:

- Buka Firefox -> `https://google.com` (harus bisa)
- Coba domain non-Google (harus diblok oleh browser policy)

## Catatan

- Ini implementasi MVP berbasis browser policy.
- Pembatasan ini fokus untuk jalur browser. Hardening lanjutan (egress firewall level container/host) bisa ditambahkan jika ingin lebih ketat.
