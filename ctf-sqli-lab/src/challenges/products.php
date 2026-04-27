<?php
$page_title = "CTF Company - Product Catalog";
include '../includes/header.php';
include '../config/database.php';

$results = [];
$debug_query = '';
$error = '';

// VULNERABLE: category parameter directly injected
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'name';

if ($category) {
    $query = "SELECT id, name, description, price, category, stock FROM products WHERE category='$category' ORDER BY $sort";
    $debug_query = $query;
    
    $result = $conn->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    } else {
        $error = "Query Error: " . $conn->error;
    }
} else {
    // Show all products
    $query = "SELECT id, name, description, price, category, stock FROM products ORDER BY $sort";
    $debug_query = $query;
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    } else {
        $error = "Query Error: " . $conn->error;
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1><span class="text-green">🛒</span> Product Catalog</h1>
        <p>CTF Company — Store Inventory System</p>
    </div>

    <!-- Objective removed in v2 -->

    <!-- Category Filter -->
    <div class="card">
        <h3 class="mono" style="font-size: 0.9rem; margin-bottom: 1rem;">🏷️ Filter Products</h3>
        
        <form method="GET" style="display: flex; gap: 0.75rem; align-items: flex-end;">
            <div class="form-group" style="flex:1; margin-bottom:0;">
                <label>category:</label>
                <input type="text" name="category" class="form-control" placeholder="e.g. Electronics, Accessories, Storage" value="<?= htmlspecialchars($category) ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>sort_by:</label>
                <input type="text" name="sort" class="form-control" placeholder="name" value="<?= htmlspecialchars($sort) ?>" style="width:150px;">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-bottom:0;">🔍 Filter</button>
        </form>

        <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">
            <a href="?category=Electronics" class="btn btn-ghost" style="font-size:0.8rem;">Electronics</a>
            <a href="?category=Accessories" class="btn btn-ghost" style="font-size:0.8rem;">Accessories</a>
            <a href="?category=Storage" class="btn btn-ghost" style="font-size:0.8rem;">Storage</a>
            <a href="?category=Networking" class="btn btn-ghost" style="font-size:0.8rem;">Networking</a>
            <a href="?" class="btn btn-ghost" style="font-size:0.8rem;">All</a>
        </div>

        <!-- debug:v2:hidden -->
        
        <?php if ($error): ?>
            <div class="alert alert-danger" style="margin-top:1rem;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>

    <!-- Results Table -->
    <div class="card">
        <h3 class="mono" style="font-size: 0.9rem; margin-bottom: 1rem;">
            📦 Results <span class="text-dim">(<?= count($results) ?> items)</span>
        </h3>
        
        <?php if (count($results) > 0): ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price (IDR)</th>
                        <th>Category</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                    <tr>
                        <td class="mono"><?= htmlspecialchars($row['id'] ?? '') ?></td>
                        <td><strong><?= htmlspecialchars($row['name'] ?? '') ?></strong></td>
                        <td class="text-dim" style="max-width:300px; font-size:0.85rem;"><?= htmlspecialchars($row['description'] ?? '') ?></td>
                        <td class="mono text-green"><?= htmlspecialchars($row['price'] ?? '') ?></td>
                        <td><span class="nav-badge badge-easy"><?= htmlspecialchars($row['category'] ?? '') ?></span></td>
                        <td class="mono"><?= htmlspecialchars($row['stock'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-dim" style="text-align:center; padding: 2rem;">No products found for this category.</p>
        <?php endif; ?>
    </div>

    <!-- hints:v2:hidden -->
</div>

<?php include '../includes/footer.php'; ?>
