<?php
$page_title = "CTF Company - Login Portal";
include '../includes/header.php';
include '../config/database.php';

$error = '';
$success = '';
$debug_query = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // VULNERABLE: Direct string concatenation - no prepared statements
    $query = "SELECT * FROM users WHERE username='$username' AND password=MD5('$password')";
    $debug_query = $query;
    
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        
        $success = "Login berhasil! Selamat datang, " . htmlspecialchars($user['full_name']);
        
        // Check if they got the flag user
        if (strpos($user['full_name'], 'FLAG{') !== false) {
            $success .= "<br><br><div class='flag-display'>" . htmlspecialchars($user['full_name']) . "</div>";
        }
        
        // Check if admin access
        if ($user['role'] === 'admin') {
            $success .= "<br><br><div class='alert alert-info'>🎯 Admin access granted! Cek semua data user di bawah.</div>";
            
            // Show all users when admin
            $all_users = $conn->query("SELECT id, username, email, role, full_name FROM users");
            if ($all_users) {
                $success .= "<table><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Full Name</th></tr>";
                while ($row = $all_users->fetch_assoc()) {
                    $success .= "<tr>";
                    $success .= "<td>" . htmlspecialchars($row['id']) . "</td>";
                    $success .= "<td class='mono'>" . htmlspecialchars($row['username']) . "</td>";
                    $success .= "<td>" . htmlspecialchars($row['email']) . "</td>";
                    $success .= "<td><span class='nav-badge " . ($row['role'] === 'admin' ? 'badge-hard' : 'badge-easy') . "'>" . htmlspecialchars($row['role']) . "</span></td>";
                    $success .= "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                    $success .= "</tr>";
                }
                $success .= "</table>";
            }
        }
    } else {
        $error = "Login gagal! Username atau password salah.";
        if ($conn->error) {
            $error .= "<br><small class='mono text-dim'>MySQL Error: " . htmlspecialchars($conn->error) . "</small>";
        }
    }
}
?>

<div class="container" style="max-width: 800px;">
    <div class="page-header">
        <h1><span class="text-green">🔐</span> Employee Login Portal</h1>
        <p>CTF Company — Internal Access</p>
    </div>

    <!-- Objective removed in v2 -->

    <!-- Login Form -->
    <div class="card">
        <h3 class="mono" style="font-size: 0.9rem; margin-bottom: 1rem;">🔑 Employee Login</h3>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>username:</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autocomplete="off">
            </div>
            <div class="form-group">
                <label>password:</label>
                <input type="text" name="password" class="form-control" placeholder="Enter password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary">→ Login</button>
        </form>

        <!-- debug:v2:hidden -->
    </div>

    <!-- hints:v2:hidden -->
</div>

<?php include '../includes/footer.php'; ?>
