<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get site settings
$settingsFile = '../site-settings.json';
$settings = [];

if (file_exists($settingsFile)) {
    $settingsJson = file_get_contents($settingsFile);
    $settings = json_decode($settingsJson, true);
}

// Get comments
$commentsFile = '../comments.json';
$comments = [];

if (file_exists($commentsFile)) {
    $commentsJson = file_get_contents($commentsFile);
    $commentsData = json_decode($commentsJson, true);

    if (isset($commentsData['comments'])) {
        $comments = $commentsData['comments'];
    }
}

// Count comments
$commentsCount = count($comments);

// Get offer link
$offerLink = isset($settings['general']['offerLink']) ? $settings['general']['offerLink'] : 'https://example.com/iphone16-offer';

// Get site name
$siteName = isset($settings['general']['siteName']) ? $settings['general']['siteName'] : 'موقع المسابقة';

// Include header
include 'includes/header.php';
?>

<div class="admin-content">
    <div class="admin-content-inner">
        <div class="admin-page-header">
            <h1>لوحة التحكم</h1>
            <p>مرحباً بك في لوحة تحكم موقع <?php echo $siteName; ?></p>
        </div>

        <div class="admin-dashboard-stats">
            <div class="admin-stat-card">
                <div class="admin-stat-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="admin-stat-content">
                    <h3>التعليقات</h3>
                    <p class="admin-stat-number"><?php echo $commentsCount; ?></p>
                </div>
                <a href="comments.php" class="admin-stat-link">إدارة التعليقات</a>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="admin-stat-content">
                    <h3>الإعدادات</h3>
                    <p class="admin-stat-text">تخصيص الموقع</p>
                </div>
                <a href="settings.php" class="admin-stat-link">تعديل الإعدادات</a>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon">
                    <i class="fas fa-link"></i>
                </div>
                <div class="admin-stat-content">
                    <h3>رابط العرض</h3>
                    <p class="admin-stat-text" dir="ltr"><?php echo substr($offerLink, 0, 30) . (strlen($offerLink) > 30 ? '...' : ''); ?></p>
                </div>
                <a href="settings.php#offer-link" class="admin-stat-link">تعديل الرابط</a>
            </div>
        </div>

        <div class="admin-dashboard-actions">
            <div class="admin-action-card">
                <h3>إجراءات سريعة</h3>
                <div class="admin-action-buttons">
                    <a href="../index.php" target="_blank" class="admin-button">
                        <i class="fas fa-eye"></i> عرض الموقع
                    </a>
                    <a href="settings.php" class="admin-button">
                        <i class="fas fa-cog"></i> الإعدادات
                    </a>
                    <a href="comments.php" class="admin-button">
                        <i class="fas fa-comments"></i> التعليقات
                    </a>
                    <a href="../og-debug.php" target="_blank" class="admin-button admin-button-info">
                        <i class="fas fa-share-alt"></i> فحص OG Tags
                    </a>
                    <a href="logout.php" class="admin-button admin-button-danger">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </a>
                </div>
            </div>
        </div>

        <div class="admin-dashboard-help">
            <h3>مساعدة</h3>
            <p>يمكنك استخدام لوحة التحكم لإدارة موقع المسابقة. يمكنك تعديل الإعدادات وإدارة التعليقات من هنا.</p>
            <p>للحصول على مساعدة إضافية، يرجى الاتصال بمسؤول النظام.</p>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>
