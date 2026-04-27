<?php
$page_title = "CTF Company - Feedback";
include '../includes/header.php';
include '../config/database.php';

$success = '';
$error = '';
$debug_query = '';
$feedbacks = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $rating = $_POST['rating'] ?? '5';
    
    // VULNERABLE: Time-based blind SQL Injection via INSERT statement
    $query = "INSERT INTO feedback (name, email, subject, message, rating) VALUES ('$name', '$email', '$subject', '$message', $rating)";
    $debug_query = $query;
    
    if ($conn->query($query)) {
        $success = "Feedback berhasil dikirim! Terima kasih.";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Show recent feedback (also vulnerable via rating parameter)
$filter_rating = $_GET['rating'] ?? '';
if ($filter_rating) {
    // VULNERABLE: Filter by rating
    $query_list = "SELECT name, subject, message, rating, submitted_at FROM feedback WHERE rating=$filter_rating ORDER BY submitted_at DESC LIMIT 10";
} else {
    $query_list = "SELECT name, subject, message, rating, submitted_at FROM feedback ORDER BY submitted_at DESC LIMIT 10";
}

$result = $conn->query($query_list);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
?>

<div class="container" style="max-width: 900px;">
    <div class="page-header">
        <h1><span class="text-green">💬</span> Customer Feedback</h1>
        <p>CTF Company — Tell us what you think</p>
    </div>

    <!-- Objective removed in v2 -->

    <!-- Feedback Form -->
    <div class="card">
        <h3 class="mono" style="font-size: 0.9rem; margin-bottom: 1rem;">📝 Submit Feedback</h3>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label>name:</label>
                    <input type="text" name="name" class="form-control" placeholder="Your name" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>email:</label>
                    <input type="text" name="email" class="form-control" placeholder="your@email.com" autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label>subject:</label>
                <input type="text" name="subject" class="form-control" placeholder="Subject" autocomplete="off">
            </div>
            <div class="form-group">
                <label>message:</label>
                <textarea name="message" class="form-control" rows="4" placeholder="Your feedback..." style="resize:vertical;"></textarea>
            </div>
            <div class="form-group">
                <label>rating (1-5):</label>
                <input type="text" name="rating" class="form-control" placeholder="5" value="5" style="width:100px;" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary">📤 Submit Feedback</button>
        </form>

        <!-- debug:v2:hidden -->
    </div>

    <!-- Recent Feedback -->
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
            <h3 class="mono" style="font-size: 0.9rem;">📊 Recent Feedback</h3>
            <div style="display:flex; gap:0.5rem;">
                <a href="?rating=5" class="btn btn-ghost" style="font-size:0.75rem;">⭐5</a>
                <a href="?rating=4" class="btn btn-ghost" style="font-size:0.75rem;">⭐4</a>
                <a href="?rating=3" class="btn btn-ghost" style="font-size:0.75rem;">⭐3</a>
                <a href="?" class="btn btn-ghost" style="font-size:0.75rem;">All</a>
            </div>
        </div>
        
        <?php if (count($feedbacks) > 0): ?>
            <?php foreach ($feedbacks as $fb): ?>
            <div style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.5rem;">
                <div style="display:flex; justify-content:space-between;">
                    <strong style="font-size: 0.9rem;"><?= htmlspecialchars($fb['name']) ?></strong>
                    <span class="text-yellow mono" style="font-size:0.8rem;">
                        <?= str_repeat('⭐', intval($fb['rating'])) ?>
                    </span>
                </div>
                <p class="text-dim" style="font-size:0.8rem; margin-top:0.2rem;"><?= htmlspecialchars($fb['subject']) ?></p>
                <p style="font-size:0.85rem; color:var(--text-secondary); margin-top:0.3rem;"><?= htmlspecialchars($fb['message']) ?></p>
                <p class="text-dim mono" style="font-size:0.7rem; margin-top:0.3rem;"><?= htmlspecialchars($fb['submitted_at']) ?></p>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-dim" style="text-align:center; padding: 1rem;">No feedback yet. Be the first!</p>
        <?php endif; ?>
    </div>

    <!-- hints:v2:hidden -->
</div>

<?php include '../includes/footer.php'; ?>
