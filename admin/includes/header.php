<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المسابقة - <?php echo isset($pageTitle) ? $pageTitle : 'الإدارة'; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="admin-styles.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-sidebar">
        <div class="admin-sidebar-header">
            <h2>لوحة التحكم</h2>
            <p><?php echo $_SESSION['admin_username']; ?></p>
        </div>
        <nav class="admin-sidebar-nav">
            <ul>
                <li>
                    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i> الرئيسية
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i> الإعدادات
                    </a>
                </li>
                <li>
                    <a href="comments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'comments.php' ? 'active' : ''; ?>">
                        <i class="fas fa-comments"></i> التعليقات
                    </a>
                </li>
                <li>
                    <a href="../index.php" target="_blank">
                        <i class="fas fa-eye"></i> عرض الموقع
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="admin-main">
        <div class="admin-header">
            <div class="admin-header-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <div class="admin-header-title">
                <?php echo isset($pageTitle) ? $pageTitle : 'لوحة التحكم'; ?>
            </div>
            <div class="admin-header-actions">
                <a href="../index.php" target="_blank" class="admin-header-action">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="logout.php" class="admin-header-action">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
