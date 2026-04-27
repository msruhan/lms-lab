<?php
$page_title = "CTF Company - Internal Portal";
include 'includes/header.php';
?>

<div class="container">
    <div class="page-header" style="text-align:center; padding: 2rem 0;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🏴‍☠️</div>
        <h1 style="font-size: 2rem; background: linear-gradient(135deg, var(--accent-green), var(--accent-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            SQL Injection CTF Lab
        </h1>
        <p style="font-size: 1rem; margin-top: 0.5rem;">Basic Penetration Testing — Capture The Flag Challenge</p>
        <p class="text-dim" style="margin-top: 0.3rem; font-family: 'JetBrains Mono', monospace; font-size: 0.8rem;">
            6 Challenges • 8 Flags • Progressive Difficulty
        </p>
    </div>

    <!-- Stats Bar -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="text-align:center; padding: 1.2rem;">
            <div class="mono text-green" style="font-size: 1.8rem; font-weight: 700;">6</div>
            <div class="text-dim" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">Challenges</div>
        </div>
        <div class="card" style="text-align:center; padding: 1.2rem;">
            <div class="mono text-yellow" style="font-size: 1.8rem; font-weight: 700;">8</div>
            <div class="text-dim" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">Flags</div>
        </div>
        <div class="card" style="text-align:center; padding: 1.2rem;">
            <div class="mono text-blue" style="font-size: 1.8rem; font-weight: 700;">800</div>
            <div class="text-dim" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">Total Points</div>
        </div>
        <div class="card" style="text-align:center; padding: 1.2rem;">
            <div class="mono text-purple" style="font-size: 1.8rem; font-weight: 700; color: var(--accent-purple);">3</div>
            <div class="text-dim" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;">Difficulty Tiers</div>
        </div>
    </div>

    <!-- Challenge Cards -->
    <h2 class="mono" style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--text-secondary);">
        <span class="text-green">$</span> Available Challenges
    </h2>

    <div class="challenge-grid">
        <!-- Challenge 1: Login Bypass -->
        <a href="/challenges/login.php" style="text-decoration:none; color:inherit;">
            <div class="challenge-card easy">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 0.5rem;">
                    <span style="font-size: 1.5rem;">🔐</span>
                    <span class="nav-badge badge-easy">Easy</span>
                </div>
                <h3>Challenge 1: Login Portal</h3>
                <p>Portal login perusahaan CTF Company. Berhasil masuk sebagai admin untuk mendapatkan flag.</p>
                <div class="challenge-meta">
                    <span class="points">⭐ 100 pts</span>
                    <span class="text-dim mono" style="font-size:0.75rem;">1 Flag</span>
                </div>
            </div>
        </a>

        <!-- Challenge 2: Product Catalog -->
        <a href="/challenges/products.php" style="text-decoration:none; color:inherit;">
            <div class="challenge-card medium">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 0.5rem;">
                    <span style="font-size: 1.5rem;">🛒</span>
                    <span class="nav-badge badge-medium">Medium</span>
                </div>
                <h3>Challenge 2: Product Catalog</h3>
                <p>Katalog produk perusahaan dengan fitur filter kategori. Temukan data tersembunyi di balik katalog ini.</p>
                <div class="challenge-meta">
                    <span class="points">⭐ 150 pts</span>
                    <span class="text-dim mono" style="font-size:0.75rem;">Multiple Flags</span>
                </div>
            </div>
        </a>

        <!-- Challenge 3: Employee Directory -->
        <a href="/challenges/employees.php" style="text-decoration:none; color:inherit;">
            <div class="challenge-card medium">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 0.5rem;">
                    <span style="font-size: 1.5rem;">👥</span>
                    <span class="nav-badge badge-medium">Medium</span>
                </div>
                <h3>Challenge 3: Employee Directory</h3>
                <p>Direktori karyawan perusahaan. Ada data sensitif yang tidak ditampilkan secara publik. Bisakah kamu mengaksesnya?</p>
                <div class="challenge-meta">
                    <span class="points">⭐ 150 pts</span>
                    <span class="text-dim mono" style="font-size:0.75rem;">1 Flag</span>
                </div>
            </div>
        </a>

        <!-- Challenge 4: News Articles -->
        <a href="/challenges/articles.php" style="text-decoration:none; color:inherit;">
            <div class="challenge-card easy">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 0.5rem;">
                    <span style="font-size: 1.5rem;">📰</span>
                    <span class="nav-badge badge-easy">Easy</span>
                </div>
                <h3>Challenge 4: News Portal</h3>
                <p>Portal berita internal perusahaan. Setiap artikel bisa diakses melalui ID-nya. Ada rahasia di balik sistem ini.</p>
                <div class="challenge-meta">
                    <span class="points">⭐ 100 pts</span>
                    <span class="text-dim mono" style="font-size:0.75rem;">1 Flag</span>
                </div>
            </div>
        </a>

        <!-- Challenge 5: Advanced Search -->
        <a href="/challenges/search_advanced.php" style="text-decoration:none; color:inherit;">
            <div class="challenge-card hard">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 0.5rem;">
                    <span style="font-size: 1.5rem;">🔍</span>
                    <span class="nav-badge badge-hard">Hard</span>
                </div>
                <h3>Challenge 5: Advanced Search</h3>
                <p>Fitur pencarian lanjutan dengan beberapa parameter input. Eksplorasi setiap parameter untuk menemukan kelemahan.</p>
                <div class="challenge-meta">
                    <span class="points">⭐ 200 pts</span>
                    <span class="text-dim mono" style="font-size:0.75rem;">1 Flag</span>
                </div>
            </div>
        </a>

        <!-- Challenge 6: Feedback -->
        <a href="/challenges/feedback.php" style="text-decoration:none; color:inherit;">
            <div class="challenge-card hard">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 0.5rem;">
                    <span style="font-size: 1.5rem;">💬</span>
                    <span class="nav-badge badge-hard">Hard</span>
                </div>
                <h3>Challenge 6: Feedback Form</h3>
                <p>Form feedback untuk pelanggan. Data disimpan ke database. Bisakah kamu memanfaatkan proses penyimpanan ini?</p>
                <div class="challenge-meta">
                    <span class="points">⭐ 200 pts</span>
                    <span class="text-dim mono" style="font-size:0.75rem;">1 Flag</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Rules -->
    <div class="card" style="margin-top: 2rem;">
        <h2 class="mono" style="font-size: 1rem; margin-bottom: 1rem;">
            <span class="text-yellow">⚡</span> Rules & Guidelines
        </h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; font-size: 0.9rem;">
            <div>
                <h4 class="text-green mono" style="font-size: 0.85rem; margin-bottom: 0.5rem;">✅ Allowed</h4>
                <ul style="list-style: none; color: var(--text-secondary);">
                    <li style="padding: 0.2rem 0;">→ Manual SQL Injection testing</li>
                    <li style="padding: 0.2rem 0;">→ Menggunakan tools seperti sqlmap, Burp Suite</li>
                    <li style="padding: 0.2rem 0;">→ Browser developer tools</li>
                    <li style="padding: 0.2rem 0;">→ Scripting (Python, Bash, dll)</li>
                    <li style="padding: 0.2rem 0;">→ Membaca source code yang terekspos</li>
                </ul>
            </div>
            <div>
                <h4 class="text-red mono" style="font-size: 0.85rem; margin-bottom: 0.5rem;">❌ Not Allowed</h4>
                <ul style="list-style: none; color: var(--text-secondary);">
                    <li style="padding: 0.2rem 0;">→ DoS / DDoS attacks</li>
                    <li style="padding: 0.2rem 0;">→ Attacking infrastructure di luar scope</li>
                    <li style="padding: 0.2rem 0;">→ Sharing flags dengan peserta lain</li>
                    <li style="padding: 0.2rem 0;">→ Menghapus/merusak database</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Flag Format -->
    <div class="card">
        <h2 class="mono" style="font-size: 1rem; margin-bottom: 0.75rem;">
            <span class="text-blue">🚩</span> Flag Format
        </h2>
        <div class="flag-display">
            FLAG{example_flag_here}
        </div>
        <p class="text-dim" style="margin-top: 0.75rem; font-size: 0.85rem; text-align: center;">
            Semua flag mengikuti format di atas. Submit flag yang kamu temukan ke platform LMS.
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
