<?php
// This file helps debug Open Graph tags and forces a refresh of the cache

// Set headers to prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Load site settings from JSON file
$settingsFile = 'site-settings.json';
$settings = [];

if (file_exists($settingsFile)) {
    $settingsJson = file_get_contents($settingsFile);
    $settings = json_decode($settingsJson, true);
}

// Get current language
require_once 'languages/language.php';
$currentLang = $lang->getCurrentLang();

// Get language-specific settings
$langSettings = [];
if (isset($settings['languages'][$currentLang])) {
    $langSettings = $settings['languages'][$currentLang];
}

// Get the current URL for OG tags
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$currentDomain = $protocol . $_SERVER['HTTP_HOST'];
$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . str_replace('og-debug.php', '', $_SERVER['REQUEST_URI']);

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

// Get OG title and description
$ogTitle = $langSettings['meta']['ogTitle'] ?? $langSettings['meta']['title'] ?? 'Win iPhone 16 for Free | Limited Time Offer';
$ogDescription = $langSettings['meta']['ogDescription'] ?? $langSettings['meta']['description'] ?? 'Get the new iPhone 16 for free! A golden opportunity to win the latest iPhone.';
$siteName = $langSettings['siteName'] ?? 'Win iPhone 16';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLang(); ?>" dir="<?php echo $lang->getDirection(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OG Tags Debug</title>
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo $settings['openGraph']['type'] ?? 'website'; ?>">
    <meta property="og:url" content="<?php echo $ogUrl; ?>">
    <meta property="og:title" content="<?php echo $ogTitle; ?>">
    <meta property="og:description" content="<?php echo $ogDescription; ?>">
    <meta property="og:image" content="<?php echo $ogImageUrl; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="<?php echo $siteName; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="<?php echo $settings['twitter']['card'] ?? 'summary_large_image'; ?>">
    <meta property="twitter:url" content="<?php echo $ogUrl; ?>">
    <meta property="twitter:title" content="<?php echo $ogTitle; ?>">
    <meta property="twitter:description" content="<?php echo $ogDescription; ?>">
    <meta property="twitter:image" content="<?php echo $twitterImageUrl; ?>">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0071e3;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        h2 {
            color: #333;
            margin-top: 30px;
        }
        .tag-info {
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #0071e3;
        }
        .tag-name {
            font-weight: bold;
            color: #0071e3;
        }
        .tag-value {
            word-break: break-all;
        }
        .image-preview {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #0071e3;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .debug-info {
            background: #f0f8ff;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 14px;
        }
        .debug-info code {
            background: #e9e9e9;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>OG Tags Debug</h1>
        <p>This page shows the current Open Graph tags that will be used when sharing your website on social media platforms like Facebook, Twitter, and WhatsApp.</p>
        
        <h2>Open Graph Tags</h2>
        
        <div class="tag-info">
            <div class="tag-name">og:type</div>
            <div class="tag-value"><?php echo $settings['openGraph']['type'] ?? 'website'; ?></div>
        </div>
        
        <div class="tag-info">
            <div class="tag-name">og:url</div>
            <div class="tag-value"><?php echo $ogUrl; ?></div>
        </div>
        
        <div class="tag-info">
            <div class="tag-name">og:title</div>
            <div class="tag-value"><?php echo $ogTitle; ?></div>
        </div>
        
        <div class="tag-info">
            <div class="tag-name">og:description</div>
            <div class="tag-value"><?php echo $ogDescription; ?></div>
        </div>
        
        <div class="tag-info">
            <div class="tag-name">og:image</div>
            <div class="tag-value"><?php echo $ogImageUrl; ?></div>
            <img src="<?php echo $ogImageUrl; ?>" alt="OG Image Preview" class="image-preview">
        </div>
        
        <div class="tag-info">
            <div class="tag-name">og:site_name</div>
            <div class="tag-value"><?php echo $siteName; ?></div>
        </div>
        
        <h2>Twitter Card Tags</h2>
        
        <div class="tag-info">
            <div class="tag-name">twitter:card</div>
            <div class="tag-value"><?php echo $settings['twitter']['card'] ?? 'summary_large_image'; ?></div>
        </div>
        
        <div class="tag-info">
            <div class="tag-name">twitter:image</div>
            <div class="tag-value"><?php echo $twitterImageUrl; ?></div>
            <?php if ($twitterImageUrl !== $ogImageUrl): ?>
            <img src="<?php echo $twitterImageUrl; ?>" alt="Twitter Image Preview" class="image-preview">
            <?php endif; ?>
        </div>
        
        <div class="debug-info">
            <p><strong>Debugging Tips:</strong></p>
            <ul>
                <li>Make sure your image URLs are absolute (starting with http:// or https://).</li>
                <li>Recommended image size for OG images is 1200×630 pixels.</li>
                <li>To force Facebook to refresh its cache, use the <a href="https://developers.facebook.com/tools/debug/" target="_blank">Facebook Sharing Debugger</a>.</li>
                <li>To check Twitter Cards, use the <a href="https://cards-dev.twitter.com/validator" target="_blank">Twitter Card Validator</a>.</li>
                <li>WhatsApp typically uses the same cache as Facebook.</li>
            </ul>
        </div>
        
        <a href="index.php" class="back-link">← Back to Homepage</a>
    </div>
</body>
</html>
