<?php
// Load language system
require_once 'languages/language.php';

// Load site settings from JSON file
$settingsFile = 'site-settings.json';
$settings = [];

if (file_exists($settingsFile)) {
    $settingsJson = file_get_contents($settingsFile);
    $settings = json_decode($settingsJson, true);
}

// Get current language
$currentLang = $lang->getCurrentLang();

// Get language-specific settings
$langSettings = [];
if (isset($settings['languages'][$currentLang])) {
    $langSettings = $settings['languages'][$currentLang];
}

// Default values if settings file is missing or invalid
if (empty($settings)) {
    $settings = [
        'general' => [
            'offerLink' => 'https://example.com/iphone16-offer',
            'countdownDays' => 3,
            'defaultLanguage' => 'ar'
        ],
        'languages' => [
            'ar' => [
                'siteName' => __('footer_logo'),
                'siteDescription' => __('meta_description')
            ],
            'en' => [
                'siteName' => 'Win iPhone 16',
                'siteDescription' => 'Get the new iPhone 16 for free!'
            ]
        ]
    ];
}

// Check if user is logged in to admin panel
$isAdmin = false;
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $isAdmin = true;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLang(); ?>" dir="<?php echo $lang->getDirection(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $langSettings['meta']['title'] ?? __('meta_title'); ?></title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo $langSettings['meta']['description'] ?? __('meta_description'); ?>">
    <meta name="keywords" content="<?php echo $langSettings['meta']['keywords'] ?? __('meta_keywords'); ?>">

    <?php if (isset($settings['meta']['robots'])): ?>
    <meta name="robots" content="<?php echo $settings['meta']['robots']; ?>">
    <?php endif; ?>

    <meta name="author" content="iPhone Giveaway">
    <meta name="language" content="<?php echo $lang->getCurrentLang(); ?>">
    <meta name="revisit-after" content="1 days">
    <meta name="geo.region" content="ME">

    <?php
    // Get the current URL for OG tags
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $currentDomain = $protocol . $_SERVER['HTTP_HOST'];
    $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // Function to convert relative image paths to absolute URLs
    function getAbsoluteImageUrl($imagePath, $domain) {
        if (strpos($imagePath, 'http://') === 0 || strpos($imagePath, 'https://') === 0) {
            return $imagePath; // Already an absolute URL
        }

        // Remove leading slash if present
        if (strpos($imagePath, '/') === 0) {
            $imagePath = substr($imagePath, 1);
        }

        return $domain . '/' . $imagePath;
    }

    // Get image paths
    $ogImage = $langSettings['meta']['ogImage'] ?? $settings['openGraph']['image'] ?? 'images/iphone16.png';
    $twitterImage = $langSettings['meta']['twitterImage'] ?? $settings['twitter']['image'] ?? 'images/iphone16.png';

    // Convert to absolute URLs
    $ogImageUrl = getAbsoluteImageUrl($ogImage, $currentDomain);
    $twitterImageUrl = getAbsoluteImageUrl($twitterImage, $currentDomain);

    // Get OG URL
    $ogUrl = $settings['openGraph']['url'] ?? $currentUrl;
    if (strpos($ogUrl, 'http://') !== 0 && strpos($ogUrl, 'https://') !== 0) {
        $ogUrl = $currentDomain . '/' . ltrim($ogUrl, '/');
    }
    ?>
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo $settings['openGraph']['type'] ?? 'website'; ?>">
    <meta property="og:url" content="<?php echo $ogUrl; ?>">
    <meta property="og:title" content="<?php echo $langSettings['meta']['ogTitle'] ?? $langSettings['meta']['title'] ?? __('meta_title'); ?>">
    <meta property="og:description" content="<?php echo $langSettings['meta']['ogDescription'] ?? $langSettings['meta']['description'] ?? __('meta_description'); ?>">
    <meta property="og:image" content="<?php echo $ogImageUrl; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="<?php echo $langSettings['siteName'] ?? __('footer_logo'); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="<?php echo $settings['twitter']['card'] ?? 'summary_large_image'; ?>">
    <meta property="twitter:url" content="<?php echo $ogUrl; ?>">
    <meta property="twitter:title" content="<?php echo $langSettings['meta']['ogTitle'] ?? $langSettings['meta']['title'] ?? __('meta_title'); ?>">
    <meta property="twitter:description" content="<?php echo $langSettings['meta']['ogDescription'] ?? $langSettings['meta']['description'] ?? __('meta_description'); ?>">
    <meta property="twitter:image" content="<?php echo $twitterImageUrl; ?>">

    <!-- Mobile Specific -->
    <meta name="theme-color" content="#0071e3">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo isset($settings['appearance']['favicon']) ? $settings['appearance']['favicon'] : 'images/favicon.ico'; ?>">
    <link rel="apple-touch-icon" href="<?php echo isset($settings['appearance']['touchIcon']) ? $settings['appearance']['touchIcon'] : 'images/touch-icon.png'; ?>">

    <!-- CSS -->
    <link rel="stylesheet" href="styles.css">

    <!-- Dynamic Styles -->
    <style>
        :root {
            --primary-color: <?php echo isset($settings['appearance']['primaryColor']) ? $settings['appearance']['primaryColor'] : '#0071e3'; ?>;
            --secondary-color: <?php echo isset($settings['appearance']['secondaryColor']) ? $settings['appearance']['secondaryColor'] : '#2997ff'; ?>;
            --button-color: <?php echo isset($settings['appearance']['buttonColor']) ? $settings['appearance']['buttonColor'] : '#0071e3'; ?>;
            --background-color: <?php echo isset($settings['appearance']['backgroundColor']) ? $settings['appearance']['backgroundColor'] : '#f5f5f7'; ?>;
            --text-color: <?php echo isset($settings['appearance']['textColor']) ? $settings['appearance']['textColor'] : '#1d1d1f'; ?>;
        }
    </style>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Product",
      "name": "iPhone 16",
      "image": "images/iphone16.png",
      "description": "<?php echo __('meta_description'); ?>",
      "brand": {
        "@type": "Brand",
        "name": "Apple"
      },
      "offers": {
        "@type": "Offer",
        "url": "https://example.com/iphone16-giveaway",
        "priceCurrency": "USD",
        "price": "0",
        "availability": "https://schema.org/LimitedAvailability",
        "seller": {
          "@type": "Organization",
          "name": "iPhone Giveaway"
        }
      }
    }
    </script>

    <!-- Translation meta tags for JavaScript -->
    <meta name="new-comment-text" content="<?php echo __('new_comment_text'); ?>">
    <meta name="comments-now-text" content="<?php echo __('comments_now'); ?>">
    <meta name="comments-verified-text" content="<?php echo __('comments_verified'); ?>">

    <?php if ($isAdmin): ?>
    <!-- Admin Panel Styles -->
    <link rel="stylesheet" href="admin/admin-styles.css">
    <?php endif; ?>
</head>
<body>
    <?php if ($isAdmin): ?>
    <!-- Admin Toolbar -->
    <div class="admin-toolbar">
        <div class="admin-toolbar-container">
            <div class="admin-toolbar-title">وضع المسؤول</div>
            <div class="admin-toolbar-actions">
                <a href="admin/dashboard.php" class="admin-button">لوحة التحكم</a>
                <a href="admin/settings.php" class="admin-button">الإعدادات</a>
                <a href="admin/comments.php" class="admin-button">التعليقات</a>
                <a href="admin/logout.php" class="admin-button admin-button-danger">تسجيل الخروج</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="#top"><?php echo $langSettings['siteName'] ?? __('footer_logo'); ?></a></h1>
            </div>
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <nav class="desktop-nav">
                <ul>
                    <li><a href="#about"><?php echo __('nav_about'); ?></a></li>
                    <li><a href="#cta-section"><?php echo __('nav_get_it'); ?></a></li>
                    <li><a href="#comments"><?php echo __('nav_comments'); ?></a></li>
                    <li class="lang-switch"><a href="<?php echo $lang->getSwitchLanguageUrl(); ?>"><?php echo __('nav_switch_language'); ?></a></li>
                </ul>
            </nav>
        </div>
        <nav class="mobile-nav">
            <ul>
                <li><a href="#about"><?php echo __('nav_about'); ?></a></li>
                <li><a href="#cta-section"><?php echo __('nav_get_it'); ?></a></li>
                <li><a href="#comments"><?php echo __('nav_comments'); ?></a></li>
                <li class="lang-switch"><a href="<?php echo $lang->getSwitchLanguageUrl(); ?>"><?php echo __('nav_switch_language'); ?></a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="top" class="hero-section">
            <div class="container">
                <div class="hero-content">
                    <h1 class="main-title"><?php echo isset($langSettings['content']['hero']['title']) ? $langSettings['content']['hero']['title'] : __('hero_title'); ?></h1>
                    <h2 class="subtitle"><?php echo isset($langSettings['content']['hero']['subtitle']) ? $langSettings['content']['hero']['subtitle'] : __('hero_subtitle'); ?></h2>

                    <a href="<?php echo $settings['general']['offerLink']; ?>" target="_blank" class="cta-button-rect"><?php echo isset($langSettings['content']['hero']['ctaText']) ? $langSettings['content']['hero']['ctaText'] : __('hero_cta'); ?></a>

                    <div class="countdown-container">
                        <h3><?php echo isset($langSettings['content']['hero']['countdownText']) ? $langSettings['content']['hero']['countdownText'] : __('hero_countdown'); ?></h3>
                        <div id="countdown" class="countdown">
                            <div class="time-block">
                                <span id="days">00</span>
                                <span class="time-label"><?php echo __('time_days'); ?></span>
                            </div>
                            <div class="time-block">
                                <span id="hours">00</span>
                                <span class="time-label"><?php echo __('time_hours'); ?></span>
                            </div>
                            <div class="time-block">
                                <span id="minutes">00</span>
                                <span class="time-label"><?php echo __('time_minutes'); ?></span>
                            </div>
                            <div class="time-block">
                                <span id="seconds">00</span>
                                <span class="time-label"><?php echo __('time_seconds'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile-only image -->
                    <div class="hero-image-mobile">
                        <img src="<?php echo $settings['appearance']['productImage'] ?? 'images/product.png'; ?>" alt="<?php echo $langSettings['siteName'] ?? 'Product'; ?>" class="product-image">
                    </div>
                </div>

                <!-- Desktop-only image -->
                <div class="hero-image-desktop">
                    <img src="<?php echo $settings['appearance']['productImage'] ?? 'images/product.png'; ?>" alt="<?php echo $langSettings['siteName'] ?? 'Product'; ?>" class="product-image">
                </div>
            </div>
        </section>

        <?php include 'sections/about.php'; ?>
        <?php include 'sections/cta.php'; ?>
        <?php include 'sections/comments.php'; ?>
    </main>

    <?php include 'sections/footer.php'; ?>

    <!-- JavaScript -->
    <script src="script.js"></script>

    <?php if ($isAdmin): ?>
    <!-- Admin Panel Scripts -->
    <script src="admin/admin-scripts.js"></script>
    <?php endif; ?>
</body>
</html>
