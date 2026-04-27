<?php
$page_title = "CTF Company - Advanced Search";
include '../includes/header.php';
include '../config/database.php';

$results = [];
$debug_query = '';
$error = '';
$total = 0;

$keyword = $_GET['q'] ?? '';
$table = $_GET['table'] ?? 'products';
$limit = $_GET['limit'] ?? '10';
$order = $_GET['order'] ?? 'ASC';

if ($keyword) {
    // VULNERABLE: Multiple injection points - table name, limit, and order are all injectable
    $query = "SELECT * FROM $table WHERE name LIKE '%$keyword%' OR description LIKE '%$keyword%' ORDER BY id $order LIMIT $limit";
    $debug_query = $query;
    
    $result = $conn->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        $total = count($results);
    } else {
        $error = $conn->error;
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1><span class="text-green">🔍</span> Advanced Search</h1>
        <p>CTF Company — Multi-Parameter Search Engine</p>
    </div>

    <!-- Objective removed in v2 -->

    <!-- Search Form -->
    <div class="card">
        <h3 class="mono" style="font-size: 0.9rem; margin-bottom: 1rem;">🔍 Advanced Search</h3>
        
        <form method="GET">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>keyword (q):</label>
                    <input type="text" name="q" class="form-control" placeholder="Search keyword..." value="<?= htmlspecialchars($keyword) ?>">
                </div>
                <div class="form-group">
                    <label>table:</label>
                    <input type="text" name="table" class="form-control" placeholder="products" value="<?= htmlspecialchars($table) ?>">
                </div>
                <div class="form-group">
                    <label>order:</label>
                    <input type="text" name="order" class="form-control" placeholder="ASC or DESC" value="<?= htmlspecialchars($order) ?>">
                </div>
                <div class="form-group">
                    <label>limit:</label>
                    <input type="text" name="limit" class="form-control" placeholder="10" value="<?= htmlspecialchars($limit) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">🔍 Search</button>
        </form>

        <!-- debug:v2:hidden -->
        
        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-top:1rem;">
                <strong>MySQL Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Results -->
    <?php if ($total > 0): ?>
    <div class="card">
        <h3 class="mono" style="font-size: 0.9rem; margin-bottom: 1rem;">
            📊 Results <span class="text-dim">(<?= $total ?> rows)</span>
        </h3>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <?php foreach (array_keys($results[0]) as $col): ?>
                        <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                    <tr>
                        <?php foreach ($row as $val): ?>
                        <td style="max-width:250px; overflow:hidden; text-overflow:ellipsis; font-size:0.85rem;">
                            <?= htmlspecialchars($val ?? 'NULL') ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- hints:v2:hidden -->
</div>

<?php include '../includes/footer.php'; ?>
