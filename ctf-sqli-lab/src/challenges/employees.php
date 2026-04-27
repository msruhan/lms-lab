<?php
$page_title = "CTF Company - Employee Directory";
include '../includes/header.php';
include '../config/database.php';

$search = $_GET['search'] ?? '';
$dept = $_GET['dept'] ?? '';
$message = '';
$found = false;
$debug_query = '';

if ($search || $dept) {
    // VULNERABLE: Boolean-based Blind SQL Injection
    if ($search) {
        $query = "SELECT emp_id, first_name, last_name, department, position_title FROM employees WHERE (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR emp_id LIKE '%$search%') AND is_active=1";
    } else {
        $query = "SELECT emp_id, first_name, last_name, department, position_title FROM employees WHERE department='$dept' AND is_active=1";
    }
    
    $debug_query = $query;
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $found = true;
        $message = '';
    } else {
        $found = false;
        if ($conn->error) {
            // Intentionally show error for error-based exploitation
            $message = "Database error: " . $conn->error;
        } else {
            $message = "Tidak ditemukan karyawan dengan kriteria tersebut.";
        }
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1><span class="text-green">👥</span> Employee Directory</h1>
        <p>CTF Company — Human Resources Portal</p>
    </div>

    <!-- Objective removed in v2 -->

    <!-- Search Form -->
    <div class="card">
        <h3 class="mono" style="font-size: 0.9rem; margin-bottom: 1rem;">🔎 Search Employee</h3>
        
        <form method="GET" style="display: flex; gap: 0.75rem; align-items: flex-end;">
            <div class="form-group" style="flex:1; margin-bottom:0;">
                <label>search_name / emp_id:</label>
                <input type="text" name="search" class="form-control" placeholder="Enter name or employee ID" value="<?= htmlspecialchars($search) ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-bottom:0;">🔍 Search</button>
        </form>

        <div style="margin-top: 0.75rem;">
            <span class="text-dim mono" style="font-size:0.8rem;">Quick filter by department:</span>
            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                <a href="?dept=IT Security" class="btn btn-ghost" style="font-size:0.8rem;">IT Security</a>
                <a href="?dept=Engineering" class="btn btn-ghost" style="font-size:0.8rem;">Engineering</a>
                <a href="?dept=Human Resources" class="btn btn-ghost" style="font-size:0.8rem;">HR</a>
                <a href="?dept=Finance" class="btn btn-ghost" style="font-size:0.8rem;">Finance</a>
            </div>
        </div>

        <!-- debug:v2:hidden -->
    </div>

    <!-- Results -->
    <div class="card">
        <h3 class="mono" style="font-size: 0.9rem; margin-bottom: 1rem;">📋 Results</h3>
        
        <?php if ($message): ?>
            <div class="alert <?= $conn->error ? 'alert-danger' : 'alert-info' ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($found && $result): ?>
        <table>
            <thead>
                <tr>
                    <th>Emp ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Department</th>
                    <th>Position</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="mono"><?= htmlspecialchars($row['emp_id']) ?></td>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><span class="nav-badge badge-easy"><?= htmlspecialchars($row['department']) ?></span></td>
                    <td class="text-dim"><?= htmlspecialchars($row['position_title']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <p class="text-dim" style="margin-top: 1rem; font-size: 0.8rem;">
            ℹ️ Salary dan contact information hanya tersedia untuk HR Manager.
        </p>
        <?php elseif ($search || $dept): ?>
            <?php if (!$conn->error): ?>
            <div style="text-align:center; padding: 2rem;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔍</div>
                <p class="text-dim">No results found</p>
                <p class="text-dim" style="font-size: 0.8rem; margin-top: 0.3rem;">
                    Status: <span class="text-red mono">Employee NOT FOUND</span>
                </p>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-dim" style="text-align:center; padding: 2rem;">Enter a search term to find employees.</p>
        <?php endif; ?>
    </div>

    <!-- hints:v2:hidden -->
</div>

<?php include '../includes/footer.php'; ?>
