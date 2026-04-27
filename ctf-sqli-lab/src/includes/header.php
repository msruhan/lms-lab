<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'CTF Company Portal' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0e17;
            --bg-secondary: #111827;
            --bg-card: #1a2332;
            --bg-card-hover: #1f2b3d;
            --accent-green: #00ff88;
            --accent-green-dim: #00cc6a;
            --accent-red: #ff4757;
            --accent-blue: #3b82f6;
            --accent-yellow: #fbbf24;
            --accent-purple: #a855f7;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --text-dim: #64748b;
            --border-color: #1e293b;
            --border-glow: rgba(0, 255, 136, 0.15);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Scanline effect */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0, 255, 136, 0.01) 2px,
                rgba(0, 255, 136, 0.01) 4px
            );
            pointer-events: none;
            z-index: 1000;
        }

        .navbar {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .navbar-logo {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent-green), var(--accent-blue));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--bg-primary);
        }

        .navbar-title {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--accent-green);
            letter-spacing: -0.02em;
        }

        .navbar-subtitle {
            font-size: 0.7rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .nav-links {
            display: flex;
            gap: 0.25rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--accent-green);
            background: rgba(0, 255, 136, 0.08);
        }

        .nav-badge {
            display: inline-block;
            padding: 0.1rem 0.5rem;
            border-radius: 10px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-easy { background: rgba(0, 255, 136, 0.15); color: var(--accent-green); }
        .badge-medium { background: rgba(251, 191, 36, 0.15); color: var(--accent-yellow); }
        .badge-hard { background: rgba(255, 71, 87, 0.15); color: var(--accent-red); }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.8rem;
            background: var(--bg-card);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .user-badge .dot {
            width: 8px;
            height: 8px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .page-header p {
            color: var(--text-dim);
            font-size: 0.9rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }

        .card:hover {
            border-color: var(--border-glow);
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.05);
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.4rem;
            font-family: 'JetBrains Mono', monospace;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 1rem;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1);
        }

        .btn {
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--accent-green);
            color: var(--bg-primary);
        }

        .btn-primary:hover {
            background: var(--accent-green-dim);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 255, 136, 0.3);
        }

        .btn-danger {
            background: var(--accent-red);
            color: white;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-ghost:hover {
            color: var(--text-primary);
            border-color: var(--text-dim);
        }

        .alert {
            padding: 1rem 1.2rem;
            border-radius: 8px;
            margin-bottom: 1.2rem;
            font-size: 0.9rem;
            border: 1px solid;
        }

        .alert-danger {
            background: rgba(255, 71, 87, 0.1);
            border-color: rgba(255, 71, 87, 0.2);
            color: var(--accent-red);
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.1);
            border-color: rgba(0, 255, 136, 0.2);
            color: var(--accent-green);
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.2);
            color: var(--accent-blue);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: var(--bg-primary);
            padding: 0.8rem 1rem;
            text-align: left;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-dim);
            border-bottom: 1px solid var(--border-color);
        }

        table td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
        }

        table tr:hover td {
            background: rgba(0, 255, 136, 0.02);
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .text-green { color: var(--accent-green); }
        .text-red { color: var(--accent-red); }
        .text-yellow { color: var(--accent-yellow); }
        .text-blue { color: var(--accent-blue); }
        .text-dim { color: var(--text-dim); }

        .hint-box {
            background: rgba(251, 191, 36, 0.05);
            border: 1px dashed rgba(251, 191, 36, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .hint-box summary {
            cursor: pointer;
            color: var(--accent-yellow);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .hint-box p {
            margin-top: 0.75rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .flag-display {
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(59, 130, 246, 0.1));
            border: 1px solid var(--accent-green);
            border-radius: 8px;
            padding: 1rem 1.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: var(--accent-green);
            text-align: center;
            animation: glow 2s ease-in-out infinite;
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 5px rgba(0, 255, 136, 0.2); }
            50% { box-shadow: 0 0 20px rgba(0, 255, 136, 0.4); }
        }

        .challenge-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.2rem;
        }

        .challenge-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .challenge-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }

        .challenge-card.easy::before { background: var(--accent-green); }
        .challenge-card.medium::before { background: var(--accent-yellow); }
        .challenge-card.hard::before { background: var(--accent-red); }

        .challenge-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-glow);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .challenge-card h3 {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .challenge-card p {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .challenge-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .points {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            color: var(--accent-yellow);
        }

        footer {
            text-align: center;
            padding: 2rem;
            color: var(--text-dim);
            font-size: 0.8rem;
            border-top: 1px solid var(--border-color);
            margin-top: 3rem;
        }

        /* SQL Debug Output */
        .sql-debug {
            background: #1a1a2e;
            border: 1px solid #2d2d4a;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: #ff6b6b;
            overflow-x: auto;
        }

        .sql-debug::before {
            content: '// DEBUG SQL Query';
            display: block;
            color: #666;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .navbar { padding: 0.75rem 1rem; flex-direction: column; gap: 0.75rem; }
            .nav-links { flex-wrap: wrap; justify-content: center; }
            .container { padding: 1rem; }
            .challenge-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="/index.php" class="navbar-brand">
            <div class="navbar-logo">⚡</div>
            <div>
                <div class="navbar-title">CTF_COMPANY</div>
                <div class="navbar-subtitle">Internal Portal v2.1.4</div>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">🏠 Home</a></li>
            <li><a href="/challenges/login.php" class="<?= basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : '' ?>">🔐 Login</a></li>
            <li><a href="/challenges/products.php" class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">🛒 Products</a></li>
            <li><a href="/challenges/employees.php" class="<?= basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : '' ?>">👥 Directory</a></li>
            <li><a href="/challenges/articles.php" class="<?= basename($_SERVER['PHP_SELF']) == 'articles.php' ? 'active' : '' ?>">📰 News</a></li>
            <li><a href="/challenges/search_advanced.php" class="<?= basename($_SERVER['PHP_SELF']) == 'search_advanced.php' ? 'active' : '' ?>">🔍 Search</a></li>
            <li><a href="/challenges/feedback.php" class="<?= basename($_SERVER['PHP_SELF']) == 'feedback.php' ? 'active' : '' ?>">💬 Feedback</a></li>
            <?php if (isset($_SESSION['user'])): ?>
            <li>
                <div class="user-badge">
                    <span class="dot"></span>
                    <span class="mono" style="font-size:0.8rem;"><?= htmlspecialchars($_SESSION['user']) ?></span>
                    <a href="/challenges/logout.php" style="color:var(--accent-red);font-size:0.75rem;">✕</a>
                </div>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
