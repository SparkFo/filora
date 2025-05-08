<?php
// Load comments from JSON file for server-side rendering (optional)
$comments = [];

// Get current language
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Load comments from the appropriate file based on language
$commentsFile = $currentLang === 'ar' ? 'data/comments-ar.json' : 'data/comments-en.json';

// Check if the file exists
if (file_exists($commentsFile)) {
    $commentsJson = file_get_contents($commentsFile);
    $commentsData = json_decode($commentsJson, true);

    if (isset($commentsData['comments']) && is_array($commentsData['comments'])) {
        $comments = $commentsData['comments'];
    }
} else {
    // Fallback to old format if new files don't exist
    $oldCommentsFile = 'comments.json';

    if (file_exists($oldCommentsFile)) {
        $commentsJson = file_get_contents($oldCommentsFile);
        $commentsData = json_decode($commentsJson, true);

        // Get comments for current language
        if (isset($commentsData['comments'][$currentLang])) {
            $comments = $commentsData['comments'][$currentLang];
        } elseif (isset($commentsData['comments']['en'])) {
            // Fallback to English if current language not available
            $comments = $commentsData['comments']['en'];
        } elseif (isset($commentsData['comments']['ar'])) {
            // Fallback to Arabic if English not available
            $comments = $commentsData['comments']['ar'];
        } elseif (isset($commentsData['comments']) && is_array($commentsData['comments'])) {
            // Legacy support for old format
            $comments = $commentsData['comments'];
        }
    }
}
?>

<section id="comments" class="comments-section">
    <div class="container">
        <h2 class="section-title"><?php echo isset($langSettings['content']['comments']['title']) ? $langSettings['content']['comments']['title'] : __('comments_title'); ?></h2>
        <div class="comments-container" id="comments-container">
            <?php if ($isAdmin): ?>
            <!-- Admin message -->
            <div class="admin-message">
                <p>Comments are loaded dynamically via JavaScript. You can manage comments from the <a href="admin/comments.php">admin panel</a>.</p>
            </div>
            <?php endif; ?>

            <!-- Comments will be dynamically added here via JavaScript -->
            <!-- If JavaScript is disabled, we can show some comments server-side -->
            <?php if (!empty($comments) && isset($_GET['nojs'])): ?>
                <?php foreach (array_slice($comments, 0, 4) as $comment): ?>
                <div class="comment">
                    <div class="comment-header">
                        <div class="comment-avatar">
                            <img src="<?php echo $comment['avatar']; ?>" alt="<?php echo $comment['name']; ?>">
                        </div>
                        <div class="comment-info">
                            <h4><?php echo $comment['name']; ?>
                                <?php if (isset($comment['verified']) && $comment['verified']): ?>
                                <span class="verified-badge"><i class="fas fa-check-circle"></i></span>
                                <?php endif; ?>
                            </h4>
                            <span class="comment-date"><?php echo date('Y-m-d', strtotime($comment['date'])); ?></span>
                            <?php if (isset($comment['location']) && $comment['location']): ?>
                            <span class="comment-location"><i class="fas fa-map-marker-alt"></i> <?php echo $comment['location']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="comment-text"><?php echo $comment['text']; ?></p>
                    <div class="comment-rating">
                        <?php echo str_repeat('★', $comment['rating']) . str_repeat('☆', 5 - $comment['rating']); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
