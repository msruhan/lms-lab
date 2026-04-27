<?php
$page_title = "CTF Company - News Portal";
include '../includes/header.php';
include '../config/database.php';

$article = null;
$articles_list = [];
$debug_query = '';
$error = '';

$id = $_GET['id'] ?? '';

if ($id) {
    // VULNERABLE: Integer-based injection without quotes - Error-based SQLi
    $query = "SELECT id, title, content, author, category, views, published_at FROM articles WHERE id=$id AND is_published=1";
    $debug_query = $query;
    
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $article = $result->fetch_assoc();
        // Update view count (also vulnerable)
        $conn->query("UPDATE articles SET views = views + 1 WHERE id=$id");
    } else {
        if ($conn->error) {
            $error = $conn->error;
        } else {
            $error = "Artikel tidak ditemukan.";
        }
    }
} else {
    // List all articles
    $result = $conn->query("SELECT id, title, author, category, views, published_at FROM articles WHERE is_published=1 ORDER BY published_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $articles_list[] = $row;
        }
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1><span class="text-green">📰</span> Company News</h1>
        <p>CTF Company — Internal News Portal</p>
    </div>

    <!-- Objective removed in v2 -->

    <?php if ($article): ?>
    <!-- Single Article View -->
    <div class="card">
        <a href="/challenges/articles.php" class="btn btn-ghost" style="font-size:0.8rem; margin-bottom: 1rem;">← Back to articles</a>
        
        <div style="display:flex; gap: 0.75rem; align-items:center; margin-bottom: 0.5rem;">
            <span class="nav-badge badge-easy"><?= htmlspecialchars($article['category']) ?></span>
            <span class="text-dim mono" style="font-size:0.75rem;"><?= htmlspecialchars($article['published_at']) ?></span>
            <span class="text-dim mono" style="font-size:0.75rem;">👁️ <?= htmlspecialchars($article['views']) ?> views</span>
        </div>
        
        <h2 style="font-size: 1.3rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($article['title']) ?></h2>
        <p class="text-dim" style="font-size: 0.85rem; margin-bottom: 1.5rem;">By <strong class="text-blue"><?= htmlspecialchars($article['author']) ?></strong></p>
        
        <div style="color: var(--text-secondary); line-height: 1.8; font-size: 0.95rem;">
            <?= htmlspecialchars($article['content']) ?>
        </div>
    </div>

    <?php elseif ($error): ?>
    <!-- Error Display -->
    <div class="card">
        <a href="/challenges/articles.php" class="btn btn-ghost" style="font-size:0.8rem; margin-bottom: 1rem;">← Back to articles</a>
        <div class="alert alert-danger">
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    </div>

    <?php else: ?>
    <!-- Articles List -->
    <div class="card">
        <h3 class="mono" style="font-size: 0.9rem; margin-bottom: 1rem;">📰 Latest Articles</h3>
        
        <?php foreach ($articles_list as $art): ?>
        <a href="?id=<?= $art['id'] ?>" style="text-decoration:none; color:inherit;">
            <div style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.75rem; transition: all 0.2s;" 
                 onmouseover="this.style.borderColor='rgba(0,255,136,0.3)'" onmouseout="this.style.borderColor='var(--border-color)'">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <div style="display:flex; gap:0.5rem; align-items:center; margin-bottom:0.3rem;">
                            <span class="nav-badge badge-easy" style="font-size:0.6rem;"><?= htmlspecialchars($art['category']) ?></span>
                            <span class="text-dim mono" style="font-size:0.7rem;"><?= htmlspecialchars($art['published_at']) ?></span>
                        </div>
                        <h4 style="font-size: 1rem;"><?= htmlspecialchars($art['title']) ?></h4>
                        <p class="text-dim" style="font-size: 0.8rem; margin-top: 0.2rem;">By <?= htmlspecialchars($art['author']) ?></p>
                    </div>
                    <div class="text-dim mono" style="font-size: 0.8rem;">
                        👁️ <?= htmlspecialchars($art['views']) ?>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- debug:v2:hidden -->

    <!-- hints:v2:hidden -->
</div>

<?php include '../includes/footer.php'; ?>
