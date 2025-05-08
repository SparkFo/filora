<?php
session_start();

// Load language system
require_once '../languages/language.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Set page title
$pageTitle = 'الإعدادات';

// Get site settings
$settingsFile = '../site-settings.json';
$settings = [];

if (file_exists($settingsFile)) {
    $settingsJson = file_get_contents($settingsFile);
    $settings = json_decode($settingsJson, true);
}

// Set default language to English
if (!isset($settings['general']['defaultLanguage'])) {
    $settings['general']['defaultLanguage'] = 'en';
}

// Initialize appearance settings if not exists
if (!isset($settings['appearance'])) {
    $settings['appearance'] = [
        'primaryColor' => '#0071e3',
        'secondaryColor' => '#2997ff',
        'buttonColor' => '#0071e3',
        'backgroundColor' => '#f5f5f7',
        'textColor' => '#1d1d1f',
        'productImage' => 'images/product.png',
        'favicon' => 'images/favicon.ico',
        'touchIcon' => 'images/touch-icon.png'
    ];
}

// Initialize languages settings if not exists
if (!isset($settings['languages'])) {
    $settings['languages'] = [
        'ar' => [
            'siteName' => isset($settings['general']['siteName']) ? $settings['general']['siteName'] : 'اربح ايفون 16 مجاناً',
            'siteDescription' => isset($settings['general']['siteDescription']) ? $settings['general']['siteDescription'] : 'احصل على ايفون 16 الجديد مجاناً!',
            'meta' => [
                'title' => isset($settings['meta']['title']) ? $settings['meta']['title'] : 'اربح ايفون 16 مجاناً | عرض محدود لفترة محدودة',
                'description' => isset($settings['meta']['description']) ? $settings['meta']['description'] : 'احصل على ايفون 16 الجديد مجاناً!',
                'keywords' => isset($settings['meta']['keywords']) ? $settings['meta']['keywords'] : 'ايفون 16, اربح ايفون, هدايا مجانية'
            ],
            'content' => isset($settings['content']) ? $settings['content'] : []
        ],
        'en' => [
            'siteName' => 'Win iPhone 16 for Free',
            'siteDescription' => 'Get the new iPhone 16 for free!',
            'meta' => [
                'title' => 'Win iPhone 16 for Free | Limited Time Offer',
                'description' => 'Get the new iPhone 16 for free! A golden opportunity to win the latest iPhone.',
                'keywords' => 'iPhone 16, win iPhone, free gifts, free iPhone, iPhone contest'
            ],
            'content' => [
                'hero' => [
                    'title' => 'Win the Brand New iPhone 16',
                    'subtitle' => 'Golden opportunity! Get it now for free',
                    'ctaText' => 'Get It Now',
                    'countdownText' => 'Offer ends in:'
                ],
                'about' => [
                    'title' => 'About the Offer',
                    'paragraph1' => 'We present you with a golden opportunity to get the latest version of Apple phones - the brand new iPhone 16!',
                    'paragraph2' => 'This offer is time-limited and available to a limited number of participants only. Get your chance now before the offer ends.',
                    'paragraph3' => 'All you have to do is click the "Get It Now" button and follow the simple steps to get your chance to win this amazing device.',
                    'features' => [
                        [
                            'icon' => 'fas fa-mobile-alt',
                            'title' => 'Latest Apple Technology',
                            'description' => 'Enjoy the latest Apple technology in the new iPhone 16'
                        ],
                        [
                            'icon' => 'fas fa-camera',
                            'title' => 'Advanced Camera',
                            'description' => 'Take professional photos with the advanced iPhone 16 camera'
                        ],
                        [
                            'icon' => 'fas fa-battery-full',
                            'title' => 'Long-lasting Battery',
                            'description' => 'Enjoy a battery that lasts all day with intensive use'
                        ]
                    ]
                ],
                'cta' => [
                    'title' => 'Get iPhone 16 for Free',
                    'buttonText' => 'Get It Now',
                    'countdownText' => 'Offer ends in:'
                ],
                'comments' => [
                    'title' => 'Previous Winners\' Reviews'
                ],
                'footer' => [
                    'logo' => 'Win iPhone 16',
                    'tagline' => 'Your chance to get the latest Apple phone for free',
                    'copyright' => '© 2024 Win iPhone 16. All rights reserved.',
                    'disclaimer1' => 'This offer is time-limited and may end at any moment. The offer is available for a limited time only.',
                    'social' => isset($settings['content']['footer']['social']) ? $settings['content']['footer']['social'] : []
                ]
            ]
        ]
    ];

    // Save the updated settings
    $settingsJson = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($settingsFile, $settingsJson);
}

// Get current language for editing
$editLang = isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en']) ? $_GET['lang'] : 'ar';

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update settings
    if (isset($_POST['update_settings'])) {
        // Get the language code from the form
        $langCode = $_POST['language_code'];

        // General settings (shared across languages)
        $settings['general']['offerLink'] = $_POST['offer_link'];
        $settings['general']['countdownDays'] = (int)$_POST['countdown_days'];
        $settings['general']['defaultLanguage'] = $_POST['default_language'] ?? 'en';

        // Appearance settings (shared across languages)
        $settings['appearance']['primaryColor'] = $_POST['primary_color'];
        $settings['appearance']['secondaryColor'] = $_POST['secondary_color'];
        $settings['appearance']['buttonColor'] = $_POST['button_color'];
        $settings['appearance']['backgroundColor'] = $_POST['background_color'];
        $settings['appearance']['textColor'] = $_POST['text_color'];
        $settings['appearance']['productImage'] = $_POST['product_image'];
        $settings['appearance']['favicon'] = $_POST['favicon'];
        $settings['appearance']['touchIcon'] = $_POST['touch_icon'];

        // Language-specific settings
        // Basic settings
        $settings['languages'][$langCode]['siteName'] = $_POST['site_name'];
        $settings['languages'][$langCode]['siteDescription'] = $_POST['site_description'];

        // Meta settings
        $settings['languages'][$langCode]['meta']['title'] = $_POST['meta_title'];
        $settings['languages'][$langCode]['meta']['description'] = $_POST['meta_description'];
        $settings['languages'][$langCode]['meta']['keywords'] = $_POST['meta_keywords'];

        // Open Graph settings (language-specific)
        $settings['languages'][$langCode]['meta']['ogTitle'] = $_POST['og_title'];
        $settings['languages'][$langCode]['meta']['ogDescription'] = $_POST['og_description'];
        $settings['languages'][$langCode]['meta']['ogImage'] = $_POST['og_image'];
        $settings['languages'][$langCode]['meta']['twitterImage'] = $_POST['twitter_image'];

        // Open Graph settings (shared)
        $settings['openGraph']['type'] = $_POST['og_type'];
        $settings['openGraph']['url'] = $_POST['og_url'];
        $settings['openGraph']['image'] = $_POST['og_image'];

        // Twitter Card settings
        $settings['twitter']['card'] = $_POST['twitter_card'];
        $settings['twitter']['url'] = $_POST['og_url']; // Use the same URL as OG
        $settings['twitter']['image'] = $_POST['twitter_image'];

        // Hero content
        $settings['languages'][$langCode]['content']['hero']['title'] = $_POST['hero_title'];
        $settings['languages'][$langCode]['content']['hero']['subtitle'] = $_POST['hero_subtitle'];
        $settings['languages'][$langCode]['content']['hero']['ctaText'] = $_POST['hero_cta_text'];
        $settings['languages'][$langCode]['content']['hero']['countdownText'] = $_POST['hero_countdown_text'];

        // About content
        $settings['languages'][$langCode]['content']['about']['title'] = $_POST['about_title'];
        $settings['languages'][$langCode]['content']['about']['paragraph1'] = $_POST['about_paragraph1'];
        $settings['languages'][$langCode]['content']['about']['paragraph2'] = $_POST['about_paragraph2'];
        $settings['languages'][$langCode]['content']['about']['paragraph3'] = $_POST['about_paragraph3'];

        // Features
        if (isset($_POST['features'])) {
            $settings['languages'][$langCode]['content']['about']['features'] = $_POST['features'];
        }

        // CTA content
        $settings['languages'][$langCode]['content']['cta']['title'] = $_POST['cta_title'];
        $settings['languages'][$langCode]['content']['cta']['buttonText'] = $_POST['cta_button_text'];
        $settings['languages'][$langCode]['content']['cta']['countdownText'] = $_POST['cta_countdown_text'];

        // Comments content
        $settings['languages'][$langCode]['content']['comments']['title'] = $_POST['comments_title'];

        // Footer content
        $settings['languages'][$langCode]['content']['footer']['logo'] = $_POST['footer_logo'];
        $settings['languages'][$langCode]['content']['footer']['tagline'] = $_POST['footer_tagline'];
        $settings['languages'][$langCode]['content']['footer']['copyright'] = $_POST['footer_copyright'];
        $settings['languages'][$langCode]['content']['footer']['disclaimer1'] = $_POST['footer_disclaimer1'];

        // Social links
        if (isset($_POST['social'])) {
            $settings['languages'][$langCode]['content']['footer']['social'] = $_POST['social'];
        }

        // Save settings to file
        $settingsJson = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($settingsFile, $settingsJson)) {
            $message = 'تم حفظ الإعدادات بنجاح';
            $messageType = 'success';
        } else {
            $message = 'حدث خطأ أثناء حفظ الإعدادات';
            $messageType = 'danger';
        }
    }
}

// Get language-specific settings
$langSettings = isset($settings['languages'][$editLang]) ? $settings['languages'][$editLang] : [];

// Include header
include 'includes/header.php';
?>

<div class="admin-content">
    <div class="admin-content-inner">
        <div class="admin-page-header">
            <h1><?php echo $editLang === 'ar' ? 'إعدادات الموقع' : 'Site Settings'; ?></h1>
            <p><?php echo $editLang === 'ar' ? 'تخصيص إعدادات موقع المسابقة' : 'Customize Giveaway Website Settings'; ?></p>
        </div>

        <?php if ($message): ?>
        <div class="admin-alert admin-alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Language Selector -->
        <div class="admin-language-selector">
            <a href="settings.php?lang=ar" class="admin-button <?php echo $editLang === 'ar' ? 'admin-button-primary' : ''; ?>">العربية</a>
            <a href="settings.php?lang=en" class="admin-button <?php echo $editLang === 'en' ? 'admin-button-primary' : ''; ?>">English</a>
        </div>

        <form id="settings-form" method="post" action="settings.php?lang=<?php echo $editLang; ?>" class="admin-settings-form">
            <input type="hidden" name="update_settings" value="1">
            <input type="hidden" name="language_code" value="<?php echo $editLang; ?>">

            <!-- General Settings (Shared) -->
            <div class="admin-form-section">
                <h3><?php echo $editLang === 'ar' ? 'الإعدادات العامة (مشتركة)' : 'General Settings (Shared)'; ?></h3>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="offer-link"><?php echo $editLang === 'ar' ? 'رابط العرض' : 'Offer Link'; ?></label>
                        <input type="text" id="offer-link" name="offer_link" value="<?php echo isset($settings['general']['offerLink']) ? $settings['general']['offerLink'] : 'https://example.com/iphone16-offer'; ?>" required>
                    </div>

                    <div class="admin-form-col">
                        <label for="countdown-days"><?php echo $editLang === 'ar' ? 'أيام العد التنازلي' : 'Countdown Days'; ?></label>
                        <input type="number" id="countdown-days" name="countdown_days" value="<?php echo isset($settings['general']['countdownDays']) ? $settings['general']['countdownDays'] : 3; ?>" min="1" max="30" required>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="default-language"><?php echo $editLang === 'ar' ? 'اللغة الافتراضية' : 'Default Language'; ?></label>
                        <select id="default-language" name="default_language">
                            <option value="ar" <?php echo (isset($settings['general']['defaultLanguage']) && $settings['general']['defaultLanguage'] === 'ar') ? 'selected' : ''; ?>><?php echo $editLang === 'ar' ? 'العربية' : 'Arabic'; ?></option>
                            <option value="en" <?php echo (isset($settings['general']['defaultLanguage']) && $settings['general']['defaultLanguage'] === 'en') ? 'selected' : ''; ?>><?php echo $editLang === 'ar' ? 'الإنجليزية' : 'English'; ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div class="admin-form-section">
                <h3><?php echo $editLang === 'ar' ? 'إعدادات المظهر' : 'Appearance Settings'; ?></h3>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="primary-color"><?php echo $editLang === 'ar' ? 'اللون الرئيسي' : 'Primary Color'; ?></label>
                        <div class="admin-color-picker">
                            <input type="color" id="primary-color" name="primary_color" value="<?php echo isset($settings['appearance']['primaryColor']) ? $settings['appearance']['primaryColor'] : '#0071e3'; ?>">
                            <input type="text" id="primary-color-text" value="<?php echo isset($settings['appearance']['primaryColor']) ? $settings['appearance']['primaryColor'] : '#0071e3'; ?>" data-color-input>
                        </div>
                    </div>

                    <div class="admin-form-col">
                        <label for="secondary-color"><?php echo $editLang === 'ar' ? 'اللون الثانوي' : 'Secondary Color'; ?></label>
                        <div class="admin-color-picker">
                            <input type="color" id="secondary-color" name="secondary_color" value="<?php echo isset($settings['appearance']['secondaryColor']) ? $settings['appearance']['secondaryColor'] : '#2997ff'; ?>">
                            <input type="text" id="secondary-color-text" value="<?php echo isset($settings['appearance']['secondaryColor']) ? $settings['appearance']['secondaryColor'] : '#2997ff'; ?>" data-color-input>
                        </div>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="button-color"><?php echo $editLang === 'ar' ? 'لون الأزرار' : 'Button Color'; ?></label>
                        <div class="admin-color-picker">
                            <input type="color" id="button-color" name="button_color" value="<?php echo isset($settings['appearance']['buttonColor']) ? $settings['appearance']['buttonColor'] : '#0071e3'; ?>">
                            <input type="text" id="button-color-text" value="<?php echo isset($settings['appearance']['buttonColor']) ? $settings['appearance']['buttonColor'] : '#0071e3'; ?>" data-color-input>
                        </div>
                    </div>

                    <div class="admin-form-col">
                        <label for="background-color"><?php echo $editLang === 'ar' ? 'لون الخلفية' : 'Background Color'; ?></label>
                        <div class="admin-color-picker">
                            <input type="color" id="background-color" name="background_color" value="<?php echo isset($settings['appearance']['backgroundColor']) ? $settings['appearance']['backgroundColor'] : '#f5f5f7'; ?>">
                            <input type="text" id="background-color-text" value="<?php echo isset($settings['appearance']['backgroundColor']) ? $settings['appearance']['backgroundColor'] : '#f5f5f7'; ?>" data-color-input>
                        </div>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="text-color"><?php echo $editLang === 'ar' ? 'لون النص' : 'Text Color'; ?></label>
                        <div class="admin-color-picker">
                            <input type="color" id="text-color" name="text_color" value="<?php echo isset($settings['appearance']['textColor']) ? $settings['appearance']['textColor'] : '#1d1d1f'; ?>">
                            <input type="text" id="text-color-text" value="<?php echo isset($settings['appearance']['textColor']) ? $settings['appearance']['textColor'] : '#1d1d1f'; ?>" data-color-input>
                        </div>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="product-image"><?php echo $editLang === 'ar' ? 'صورة المنتج' : 'Product Image'; ?></label>
                        <div class="admin-file-upload">
                            <input type="text" id="product-image" name="product_image" value="<?php echo isset($settings['appearance']['productImage']) ? $settings['appearance']['productImage'] : 'images/product.png'; ?>">
                            <div class="admin-image-preview">
                                <img src="../<?php echo isset($settings['appearance']['productImage']) ? $settings['appearance']['productImage'] : 'images/product.png'; ?>" alt="Product Preview" id="product-image-preview">
                            </div>
                            <p class="admin-form-help"><?php echo $editLang === 'ar' ? 'أدخل مسار الصورة النسبي (مثال: images/product.png)' : 'Enter relative image path (e.g. images/product.png)'; ?></p>
                        </div>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="favicon"><?php echo $editLang === 'ar' ? 'أيقونة الموقع (Favicon)' : 'Favicon'; ?></label>
                        <div class="admin-file-upload">
                            <input type="text" id="favicon" name="favicon" value="<?php echo isset($settings['appearance']['favicon']) ? $settings['appearance']['favicon'] : 'images/favicon.ico'; ?>">
                            <div class="admin-image-preview admin-image-preview-small">
                                <img src="../<?php echo isset($settings['appearance']['favicon']) ? $settings['appearance']['favicon'] : 'images/favicon.ico'; ?>" alt="Favicon Preview" id="favicon-preview">
                            </div>
                            <p class="admin-form-help"><?php echo $editLang === 'ar' ? 'أدخل مسار الأيقونة النسبي (مثال: images/favicon.ico)' : 'Enter relative favicon path (e.g. images/favicon.ico)'; ?></p>
                        </div>
                    </div>

                    <div class="admin-form-col">
                        <label for="touch-icon"><?php echo $editLang === 'ar' ? 'أيقونة اللمس (Touch Icon)' : 'Touch Icon'; ?></label>
                        <div class="admin-file-upload">
                            <input type="text" id="touch-icon" name="touch_icon" value="<?php echo isset($settings['appearance']['touchIcon']) ? $settings['appearance']['touchIcon'] : 'images/touch-icon.png'; ?>">
                            <div class="admin-image-preview admin-image-preview-small">
                                <img src="../<?php echo isset($settings['appearance']['touchIcon']) ? $settings['appearance']['touchIcon'] : 'images/touch-icon.png'; ?>" alt="Touch Icon Preview" id="touch-icon-preview">
                            </div>
                            <p class="admin-form-help"><?php echo $editLang === 'ar' ? 'أدخل مسار الأيقونة النسبي (مثال: images/touch-icon.png)' : 'Enter relative icon path (e.g. images/touch-icon.png)'; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Language-specific Settings -->
            <div class="admin-form-section">
                <h3><?php echo $editLang === 'ar' ? 'إعدادات اللغة' : 'Language Settings'; ?> (<?php echo $editLang === 'ar' ? 'العربية' : 'English'; ?>)</h3>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="site-name"><?php echo $editLang === 'ar' ? 'اسم الموقع' : 'Site Name'; ?></label>
                        <input type="text" id="site-name" name="site_name" value="<?php echo isset($langSettings['siteName']) ? $langSettings['siteName'] : ($editLang === 'ar' ? 'اربح ايفون 16 مجاناً' : 'Win iPhone 16 for Free'); ?>" required>
                    </div>

                    <div class="admin-form-col">
                        <label for="site-description"><?php echo $editLang === 'ar' ? 'وصف الموقع' : 'Site Description'; ?></label>
                        <input type="text" id="site-description" name="site_description" value="<?php echo isset($langSettings['siteDescription']) ? $langSettings['siteDescription'] : ($editLang === 'ar' ? 'احصل على ايفون 16 الجديد مجاناً!' : 'Get the new iPhone 16 for free!'); ?>">
                    </div>
                </div>
            </div>

            <!-- Meta Settings -->
            <div class="admin-form-section">
                <h3><?php echo $editLang === 'ar' ? 'إعدادات SEO' : 'SEO Settings'; ?></h3>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="meta-title"><?php echo $editLang === 'ar' ? 'عنوان الصفحة' : 'Page Title'; ?></label>
                        <input type="text" id="meta-title" name="meta_title" value="<?php echo isset($langSettings['meta']['title']) ? $langSettings['meta']['title'] : ($editLang === 'ar' ? 'اربح ايفون 16 مجاناً | عرض محدود لفترة محدودة' : 'Win iPhone 16 for Free | Limited Time Offer'); ?>" required>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="meta-description"><?php echo $editLang === 'ar' ? 'وصف الصفحة' : 'Page Description'; ?></label>
                        <textarea id="meta-description" name="meta_description" rows="3"><?php echo isset($langSettings['meta']['description']) ? $langSettings['meta']['description'] : ($editLang === 'ar' ? 'احصل على ايفون 16 الجديد مجاناً! فرصة ذهبية محدودة للفوز بأحدث هاتف ايفون 16.' : 'Get the new iPhone 16 for free! A golden opportunity to win the latest iPhone 16.'); ?></textarea>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="meta-keywords"><?php echo $editLang === 'ar' ? 'الكلمات المفتاحية' : 'Keywords'; ?></label>
                        <textarea id="meta-keywords" name="meta_keywords" rows="3"><?php echo isset($langSettings['meta']['keywords']) ? $langSettings['meta']['keywords'] : ($editLang === 'ar' ? 'ايفون 16, اربح ايفون, هدايا مجانية, ايفون مجاني, مسابقة ايفون, آيفون 16 برو, آبل, جوائز, سحب الكتروني, عرض محدود' : 'iPhone 16, win iPhone, free gifts, free iPhone, iPhone contest, iPhone 16 Pro, Apple, prizes, electronic draw, limited offer'); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Open Graph Tags Settings -->
            <div class="admin-form-section">
                <h3><?php echo $editLang === 'ar' ? 'إعدادات Open Graph (OG Tags)' : 'Open Graph Settings (OG Tags)'; ?></h3>
                <p class="admin-section-description"><?php echo $editLang === 'ar' ? 'تستخدم هذه الإعدادات عند مشاركة الموقع على وسائل التواصل الاجتماعي مثل فيسبوك وتويتر.' : 'These settings are used when sharing the website on social media platforms like Facebook and Twitter.'; ?></p>
                <div class="admin-form-help-box">
                    <p><?php echo $editLang === 'ar' ? 'للتحقق من صحة OG Tags وكيفية ظهورها عند المشاركة:' : 'To verify OG Tags and how they appear when shared:'; ?></p>
                    <a href="../og-debug.php" target="_blank" class="admin-button admin-button-info">
                        <i class="fas fa-share-alt"></i> <?php echo $editLang === 'ar' ? 'فحص OG Tags' : 'Check OG Tags'; ?>
                    </a>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="og-title"><?php echo $editLang === 'ar' ? 'عنوان المشاركة' : 'Share Title'; ?></label>
                        <input type="text" id="og-title" name="og_title" value="<?php echo isset($langSettings['meta']['ogTitle']) ? $langSettings['meta']['ogTitle'] : (isset($langSettings['meta']['title']) ? $langSettings['meta']['title'] : ($editLang === 'ar' ? 'اربح ايفون 16 مجاناً | عرض محدود لفترة محدودة' : 'Win iPhone 16 for Free | Limited Time Offer')); ?>">
                        <p class="admin-form-help"><?php echo $editLang === 'ar' ? 'العنوان الذي سيظهر عند مشاركة الموقع على وسائل التواصل الاجتماعي' : 'The title that appears when sharing the website on social media'; ?></p>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="og-description"><?php echo $editLang === 'ar' ? 'وصف المشاركة' : 'Share Description'; ?></label>
                        <textarea id="og-description" name="og_description" rows="3"><?php echo isset($langSettings['meta']['ogDescription']) ? $langSettings['meta']['ogDescription'] : (isset($langSettings['meta']['description']) ? $langSettings['meta']['description'] : ($editLang === 'ar' ? 'احصل على ايفون 16 الجديد مجاناً! فرصة ذهبية محدودة للفوز بأحدث هاتف ايفون.' : 'Get the new iPhone 16 for free! A golden opportunity to win the latest iPhone.')); ?></textarea>
                        <p class="admin-form-help"><?php echo $editLang === 'ar' ? 'الوصف الذي سيظهر عند مشاركة الموقع على وسائل التواصل الاجتماعي' : 'The description that appears when sharing the website on social media'; ?></p>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="og-image"><?php echo $editLang === 'ar' ? 'صورة المشاركة' : 'Share Image'; ?></label>
                        <div class="admin-file-upload">
                            <input type="text" id="og-image" name="og_image" value="<?php echo isset($langSettings['meta']['ogImage']) ? $langSettings['meta']['ogImage'] : (isset($settings['openGraph']['image']) ? $settings['openGraph']['image'] : 'images/iphone16.png'); ?>">
                            <div class="admin-image-preview">
                                <img src="../<?php echo isset($langSettings['meta']['ogImage']) ? $langSettings['meta']['ogImage'] : (isset($settings['openGraph']['image']) ? $settings['openGraph']['image'] : 'images/iphone16.png'); ?>" alt="OG Image Preview" id="og-image-preview">
                            </div>
                            <p class="admin-form-help"><?php echo $editLang === 'ar' ? 'الصورة التي ستظهر عند مشاركة الموقع على وسائل التواصل الاجتماعي (يفضل أن تكون بأبعاد 1200×630 بكسل)' : 'The image that appears when sharing the website on social media (recommended size: 1200×630 pixels)'; ?></p>
                        </div>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="og-type"><?php echo $editLang === 'ar' ? 'نوع المحتوى' : 'Content Type'; ?></label>
                        <select id="og-type" name="og_type">
                            <option value="website" <?php echo (isset($settings['openGraph']['type']) && $settings['openGraph']['type'] === 'website') ? 'selected' : ''; ?>><?php echo $editLang === 'ar' ? 'موقع ويب' : 'Website'; ?></option>
                            <option value="article" <?php echo (isset($settings['openGraph']['type']) && $settings['openGraph']['type'] === 'article') ? 'selected' : ''; ?>><?php echo $editLang === 'ar' ? 'مقالة' : 'Article'; ?></option>
                            <option value="product" <?php echo (isset($settings['openGraph']['type']) && $settings['openGraph']['type'] === 'product') ? 'selected' : ''; ?>><?php echo $editLang === 'ar' ? 'منتج' : 'Product'; ?></option>
                        </select>
                        <p class="admin-form-help"><?php echo $editLang === 'ar' ? 'نوع المحتوى الذي سيتم مشاركته' : 'The type of content being shared'; ?></p>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="og-url"><?php echo $editLang === 'ar' ? 'رابط المشاركة' : 'Share URL'; ?></label>
                        <input type="text" id="og-url" name="og_url" value="<?php echo isset($settings['openGraph']['url']) ? $settings['openGraph']['url'] : 'https://example.com/iphone16-giveaway'; ?>">
                        <p class="admin-form-help"><?php echo $editLang === 'ar' ? 'الرابط الذي سيتم مشاركته (عادة ما يكون رابط الموقع الرئيسي)' : 'The URL to be shared (usually the main website URL)'; ?></p>
                    </div>
                </div>

                <h4><?php echo $editLang === 'ar' ? 'إعدادات تويتر (Twitter Cards)' : 'Twitter Card Settings'; ?></h4>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="twitter-card"><?php echo $editLang === 'ar' ? 'نوع بطاقة تويتر' : 'Twitter Card Type'; ?></label>
                        <select id="twitter-card" name="twitter_card">
                            <option value="summary_large_image" <?php echo (isset($settings['twitter']['card']) && $settings['twitter']['card'] === 'summary_large_image') ? 'selected' : ''; ?>><?php echo $editLang === 'ar' ? 'صورة كبيرة مع ملخص' : 'Summary with Large Image'; ?></option>
                            <option value="summary" <?php echo (isset($settings['twitter']['card']) && $settings['twitter']['card'] === 'summary') ? 'selected' : ''; ?>><?php echo $editLang === 'ar' ? 'ملخص' : 'Summary'; ?></option>
                            <option value="app" <?php echo (isset($settings['twitter']['card']) && $settings['twitter']['card'] === 'app') ? 'selected' : ''; ?>><?php echo $editLang === 'ar' ? 'تطبيق' : 'App'; ?></option>
                        </select>
                        <p class="admin-form-help"><?php echo $editLang === 'ar' ? 'نوع بطاقة تويتر التي ستظهر عند مشاركة الموقع على تويتر' : 'The type of Twitter card that appears when sharing the website on Twitter'; ?></p>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="twitter-image"><?php echo $editLang === 'ar' ? 'صورة تويتر' : 'Twitter Image'; ?></label>
                        <div class="admin-file-upload">
                            <input type="text" id="twitter-image" name="twitter_image" value="<?php echo isset($langSettings['meta']['twitterImage']) ? $langSettings['meta']['twitterImage'] : (isset($settings['twitter']['image']) ? $settings['twitter']['image'] : 'images/iphone16.png'); ?>">
                            <div class="admin-image-preview">
                                <img src="../<?php echo isset($langSettings['meta']['twitterImage']) ? $langSettings['meta']['twitterImage'] : (isset($settings['twitter']['image']) ? $settings['twitter']['image'] : 'images/iphone16.png'); ?>" alt="Twitter Image Preview" id="twitter-image-preview">
                            </div>
                            <p class="admin-form-help"><?php echo $editLang === 'ar' ? 'الصورة التي ستظهر عند مشاركة الموقع على تويتر (يمكن أن تكون نفس صورة OG)' : 'The image that appears when sharing the website on Twitter (can be the same as OG image)'; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Section -->
            <div class="admin-form-section">
                <h3><?php echo $editLang === 'ar' ? 'قسم البداية' : 'Hero Section'; ?></h3>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="hero-title"><?php echo $editLang === 'ar' ? 'العنوان الرئيسي' : 'Main Title'; ?></label>
                        <input type="text" id="hero-title" name="hero_title" value="<?php echo isset($langSettings['content']['hero']['title']) ? $langSettings['content']['hero']['title'] : ($editLang === 'ar' ? 'اربح ايفون 16 الجديد كلياً' : 'Win the Brand New iPhone 16'); ?>" required>
                    </div>

                    <div class="admin-form-col">
                        <label for="hero-subtitle"><?php echo $editLang === 'ar' ? 'العنوان الفرعي' : 'Subtitle'; ?></label>
                        <input type="text" id="hero-subtitle" name="hero_subtitle" value="<?php echo isset($langSettings['content']['hero']['subtitle']) ? $langSettings['content']['hero']['subtitle'] : ($editLang === 'ar' ? 'فرصة ذهبية محدودة! احصل عليه الآن مجاناً' : 'Golden opportunity! Get it now for free'); ?>">
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="hero-cta-text"><?php echo $editLang === 'ar' ? 'نص زر الدعوة للعمل' : 'CTA Button Text'; ?></label>
                        <input type="text" id="hero-cta-text" name="hero_cta_text" value="<?php echo isset($langSettings['content']['hero']['ctaText']) ? $langSettings['content']['hero']['ctaText'] : ($editLang === 'ar' ? 'احصل عليه الآن' : 'Get It Now'); ?>" required>
                    </div>

                    <div class="admin-form-col">
                        <label for="hero-countdown-text"><?php echo $editLang === 'ar' ? 'نص العد التنازلي' : 'Countdown Text'; ?></label>
                        <input type="text" id="hero-countdown-text" name="hero_countdown_text" value="<?php echo isset($langSettings['content']['hero']['countdownText']) ? $langSettings['content']['hero']['countdownText'] : ($editLang === 'ar' ? 'ينتهي العرض خلال:' : 'Offer ends in:'); ?>">
                    </div>
                </div>
            </div>

            <!-- About Section -->
            <div class="admin-form-section">
                <h3><?php echo $editLang === 'ar' ? 'قسم عن العرض' : 'About Section'; ?></h3>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="about-title"><?php echo $editLang === 'ar' ? 'عنوان القسم' : 'Section Title'; ?></label>
                        <input type="text" id="about-title" name="about_title" value="<?php echo isset($langSettings['content']['about']['title']) ? $langSettings['content']['about']['title'] : ($editLang === 'ar' ? 'عن العرض' : 'About the Offer'); ?>" required>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="about-paragraph1"><?php echo $editLang === 'ar' ? 'الفقرة الأولى' : 'First Paragraph'; ?></label>
                        <textarea id="about-paragraph1" name="about_paragraph1" rows="3"><?php echo isset($langSettings['content']['about']['paragraph1']) ? $langSettings['content']['about']['paragraph1'] : ($editLang === 'ar' ? 'نقدم لكم فرصة ذهبية للحصول على أحدث إصدار من هواتف آبل - ايفون 16 الجديد كلياً!' : 'We present you with a golden opportunity to get the latest version of Apple phones - the brand new iPhone 16!'); ?></textarea>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="about-paragraph2"><?php echo $editLang === 'ar' ? 'الفقرة الثانية' : 'Second Paragraph'; ?></label>
                        <textarea id="about-paragraph2" name="about_paragraph2" rows="3"><?php echo isset($langSettings['content']['about']['paragraph2']) ? $langSettings['content']['about']['paragraph2'] : ($editLang === 'ar' ? 'هذا العرض محدود بالوقت ومتاح لعدد محدود من المشاركين فقط. احصل على فرصتك الآن قبل انتهاء العرض.' : 'This offer is time-limited and available to a limited number of participants only. Get your chance now before the offer ends.'); ?></textarea>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="about-paragraph3"><?php echo $editLang === 'ar' ? 'الفقرة الثالثة' : 'Third Paragraph'; ?></label>
                        <textarea id="about-paragraph3" name="about_paragraph3" rows="3"><?php echo isset($langSettings['content']['about']['paragraph3']) ? $langSettings['content']['about']['paragraph3'] : ($editLang === 'ar' ? 'كل ما عليك فعله هو النقر على زر "احصل عليه الآن" واتباع الخطوات البسيطة للحصول على فرصتك في الفوز بهذا الجهاز الرائع.' : 'All you have to do is click the "Get It Now" button and follow the simple steps to get your chance to win this amazing device.'); ?></textarea>
                    </div>
                </div>

                <h4><?php echo $editLang === 'ar' ? 'الميزات' : 'Features'; ?></h4>
                <div id="features-container">
                    <?php if (isset($langSettings['content']['about']['features'])): ?>
                        <?php foreach ($langSettings['content']['about']['features'] as $index => $feature): ?>
                            <div class="admin-feature-item">
                                <div class="admin-form-row">
                                    <div class="admin-form-col">
                                        <label for="feature-icon-<?php echo $index; ?>"><?php echo $editLang === 'ar' ? 'أيقونة الميزة' : 'Feature Icon'; ?></label>
                                        <input type="text" id="feature-icon-<?php echo $index; ?>" name="features[<?php echo $index; ?>][icon]" value="<?php echo $feature['icon']; ?>" placeholder="<?php echo $editLang === 'ar' ? 'مثال: fas fa-mobile-alt' : 'Example: fas fa-mobile-alt'; ?>">
                                    </div>
                                    <div class="admin-form-col">
                                        <label for="feature-title-<?php echo $index; ?>"><?php echo $editLang === 'ar' ? 'عنوان الميزة' : 'Feature Title'; ?></label>
                                        <input type="text" id="feature-title-<?php echo $index; ?>" name="features[<?php echo $index; ?>][title]" value="<?php echo $feature['title']; ?>" placeholder="<?php echo $editLang === 'ar' ? 'عنوان الميزة' : 'Feature title'; ?>">
                                    </div>
                                </div>
                                <div class="admin-form-row">
                                    <div class="admin-form-col">
                                        <label for="feature-description-<?php echo $index; ?>"><?php echo $editLang === 'ar' ? 'وصف الميزة' : 'Feature Description'; ?></label>
                                        <input type="text" id="feature-description-<?php echo $index; ?>" name="features[<?php echo $index; ?>][description]" value="<?php echo $feature['description']; ?>" placeholder="<?php echo $editLang === 'ar' ? 'وصف الميزة' : 'Feature description'; ?>">
                                    </div>
                                    <div class="admin-form-col admin-form-col-actions">
                                        <button type="button" class="admin-button admin-button-danger remove-feature"><?php echo $editLang === 'ar' ? 'حذف' : 'Delete'; ?></button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" id="add-feature" class="admin-button"><?php echo $editLang === 'ar' ? 'إضافة ميزة' : 'Add Feature'; ?></button>
            </div>

            <!-- CTA Section -->
            <div class="admin-form-section">
                <h3><?php echo $editLang === 'ar' ? 'قسم الدعوة للعمل' : 'Call to Action Section'; ?></h3>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="cta-title"><?php echo $editLang === 'ar' ? 'عنوان القسم' : 'Section Title'; ?></label>
                        <input type="text" id="cta-title" name="cta_title" value="<?php echo isset($langSettings['content']['cta']['title']) ? $langSettings['content']['cta']['title'] : ($editLang === 'ar' ? 'احصل على ايفون 16 مجاناً' : 'Get iPhone 16 for Free'); ?>" required>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="cta-button-text"><?php echo $editLang === 'ar' ? 'نص الزر' : 'Button Text'; ?></label>
                        <input type="text" id="cta-button-text" name="cta_button_text" value="<?php echo isset($langSettings['content']['cta']['buttonText']) ? $langSettings['content']['cta']['buttonText'] : ($editLang === 'ar' ? 'احصل عليه الآن' : 'Get It Now'); ?>" required>
                    </div>

                    <div class="admin-form-col">
                        <label for="cta-countdown-text"><?php echo $editLang === 'ar' ? 'نص العد التنازلي' : 'Countdown Text'; ?></label>
                        <input type="text" id="cta-countdown-text" name="cta_countdown_text" value="<?php echo isset($langSettings['content']['cta']['countdownText']) ? $langSettings['content']['cta']['countdownText'] : ($editLang === 'ar' ? 'ينتهي العرض خلال:' : 'Offer ends in:'); ?>">
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="admin-form-section">
                <h3><?php echo $editLang === 'ar' ? 'قسم التعليقات' : 'Comments Section'; ?></h3>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="comments-title"><?php echo $editLang === 'ar' ? 'عنوان القسم' : 'Section Title'; ?></label>
                        <input type="text" id="comments-title" name="comments_title" value="<?php echo isset($langSettings['content']['comments']['title']) ? $langSettings['content']['comments']['title'] : ($editLang === 'ar' ? 'آراء الفائزين السابقين' : 'Previous Winners\' Reviews'); ?>" required>
                    </div>
                </div>

                <div class="admin-comments-preview">
                    <h4><?php echo $editLang === 'ar' ? 'معاينة التعليقات' : 'Comments Preview'; ?></h4>

                    <?php
                    // Load comments from separate JSON files
                    $commentsArFile = '../data/comments-ar.json';
                    $commentsEnFile = '../data/comments.json';
                    $comments = [
                        'ar' => [],
                        'en' => []
                    ];

                    // Check if data directory exists, if not create it
                    if (!file_exists('../data')) {
                        mkdir('../data', 0755, true);
                    }

                    // Load Arabic comments
                    if (file_exists($commentsArFile)) {
                        try {
                            $commentsJson = file_get_contents($commentsArFile);
                            $commentsData = json_decode($commentsJson, true);

                            if (isset($commentsData['comments']) && is_array($commentsData['comments'])) {
                                $comments['ar'] = $commentsData['comments'];
                            }
                        } catch (Exception $e) {
                            // Handle error silently
                        }
                    }

                    // Load English comments
                    $commentsEnFile = '../data/comments-en.json';
                    if (file_exists($commentsEnFile)) {
                        try {
                            $commentsJson = file_get_contents($commentsEnFile);
                            $commentsData = json_decode($commentsJson, true);

                            if (isset($commentsData['comments']) && is_array($commentsData['comments'])) {
                                $comments['en'] = $commentsData['comments'];
                            }
                        } catch (Exception $e) {
                            // Handle error silently
                        }
                    }

                    // Fallback to old format if needed
                    $oldCommentsFile = '../data/comments.json';
                    if ((empty($comments['ar']) || empty($comments['en'])) && file_exists($oldCommentsFile)) {
                        try {
                            $commentsJson = file_get_contents($oldCommentsFile);
                            $commentsData = json_decode($commentsJson, true);

                            if (isset($commentsData['comments'])) {
                                // Check if comments are in the old format (with language keys)
                                if (isset($commentsData['comments']['ar']) && empty($comments['ar'])) {
                                    $comments['ar'] = $commentsData['comments']['ar'];
                                }
                                if (isset($commentsData['comments']['en']) && empty($comments['en'])) {
                                    $comments['en'] = $commentsData['comments']['en'];
                                } else if (!isset($commentsData['comments']['ar']) && !isset($commentsData['comments']['en']) && empty($comments['ar'])) {
                                    // Legacy format - move all comments to Arabic
                                    $comments['ar'] = $commentsData['comments'];
                                }
                            }
                        } catch (Exception $e) {
                            // Handle error silently
                        }
                    }

                    // If comments still don't exist in data directory, check if they exist in root
                    if ((empty($comments['ar']) || empty($comments['en'])) && file_exists('../comments.json')) {
                        try {
                            $commentsJson = file_get_contents('../comments.json');
                            $commentsData = json_decode($commentsJson, true);

                            if (isset($commentsData['comments'])) {
                                // Check if comments are in the old format (with language keys)
                                if (isset($commentsData['comments']['ar']) && empty($comments['ar'])) {
                                    $comments['ar'] = $commentsData['comments']['ar'];
                                }
                                if (isset($commentsData['comments']['en']) && empty($comments['en'])) {
                                    $comments['en'] = $commentsData['comments']['en'];
                                } else if (!isset($commentsData['comments']['ar']) && !isset($commentsData['comments']['en']) && empty($comments['ar'])) {
                                    // Legacy format - move all comments to Arabic
                                    $comments['ar'] = $commentsData['comments'];
                                }
                            }
                        } catch (Exception $e) {
                            // Handle error silently
                        }
                    }

                    // Display comments count
                    $arCount = count($comments['ar']);
                    $enCount = count($comments['en']);
                    $totalCount = $arCount + $enCount;
                    ?>

                    <div class="admin-comments-stats">
                        <div class="admin-stat-item">
                            <span class="admin-stat-value"><?php echo $totalCount; ?></span>
                            <span class="admin-stat-label"><?php echo $editLang === 'ar' ? 'إجمالي التعليقات' : 'Total Comments'; ?></span>
                        </div>
                        <div class="admin-stat-item">
                            <span class="admin-stat-value"><?php echo $arCount; ?></span>
                            <span class="admin-stat-label"><?php echo $editLang === 'ar' ? 'تعليقات عربية' : 'Arabic Comments'; ?></span>
                        </div>
                        <div class="admin-stat-item">
                            <span class="admin-stat-value"><?php echo $enCount; ?></span>
                            <span class="admin-stat-label"><?php echo $editLang === 'ar' ? 'تعليقات إنجليزية' : 'English Comments'; ?></span>
                        </div>
                    </div>

                    <?php if ($totalCount > 0): ?>
                        <div class="admin-comments-sample">
                            <h5><?php echo $editLang === 'ar' ? 'عينة من التعليقات' : 'Sample Comments'; ?></h5>

                            <?php if ($arCount > 0): ?>
                                <div class="admin-comment-preview">
                                    <h6><?php echo $editLang === 'ar' ? 'تعليق عربي' : 'Arabic Comment'; ?></h6>
                                    <?php
                                    try {
                                        $arSample = $comments['ar'][0];
                                        // Check if avatar URL is absolute or relative
                                        $avatarUrl = isset($arSample['avatar']) ? $arSample['avatar'] : '../images/avatars/avatar1.png';
                                        if (!preg_match('/^https?:\/\//', $avatarUrl)) {
                                            $avatarUrl = '../' . $avatarUrl;
                                        }
                                        ?>
                                        <div class="admin-comment-preview-item">
                                            <div class="admin-comment-preview-header">
                                                <img src="<?php echo $avatarUrl; ?>" alt="<?php echo isset($arSample['name']) ? $arSample['name'] : 'User'; ?>" class="admin-comment-preview-avatar">
                                                <div>
                                                    <div class="admin-comment-preview-name"><?php echo isset($arSample['name']) ? $arSample['name'] : 'User'; ?></div>
                                                    <div class="admin-comment-preview-meta">
                                                        <span><?php echo isset($arSample['date']) ? date('Y-m-d', strtotime($arSample['date'])) : date('Y-m-d'); ?></span>
                                                        <span><?php echo isset($arSample['rating']) ? str_repeat('★', $arSample['rating']) . str_repeat('☆', 5 - $arSample['rating']) : '★★★★★'; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="admin-comment-preview-text"><?php echo isset($arSample['text']) ? (substr($arSample['text'], 0, 100) . (strlen($arSample['text']) > 100 ? '...' : '')) : 'No comment text'; ?></div>
                                        </div>
                                    <?php
                                    } catch (Exception $e) {
                                        echo '<p class="admin-note">' . ($editLang === 'ar' ? 'خطأ في عرض التعليق' : 'Error displaying comment') . '</p>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($enCount > 0): ?>
                                <div class="admin-comment-preview">
                                    <h6><?php echo $editLang === 'ar' ? 'تعليق إنجليزي' : 'English Comment'; ?></h6>
                                    <?php
                                    try {
                                        $enSample = $comments['en'][0];
                                        // Check if avatar URL is absolute or relative
                                        $avatarUrl = isset($enSample['avatar']) ? $enSample['avatar'] : '../images/avatars/avatar2.png';
                                        if (!preg_match('/^https?:\/\//', $avatarUrl)) {
                                            $avatarUrl = '../' . $avatarUrl;
                                        }
                                        ?>
                                        <div class="admin-comment-preview-item">
                                            <div class="admin-comment-preview-header">
                                                <img src="<?php echo $avatarUrl; ?>" alt="<?php echo isset($enSample['name']) ? $enSample['name'] : 'User'; ?>" class="admin-comment-preview-avatar">
                                                <div>
                                                    <div class="admin-comment-preview-name"><?php echo isset($enSample['name']) ? $enSample['name'] : 'User'; ?></div>
                                                    <div class="admin-comment-preview-meta">
                                                        <span><?php echo isset($enSample['date']) ? date('Y-m-d', strtotime($enSample['date'])) : date('Y-m-d'); ?></span>
                                                        <span><?php echo isset($enSample['rating']) ? str_repeat('★', $enSample['rating']) . str_repeat('☆', 5 - $enSample['rating']) : '★★★★★'; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="admin-comment-preview-text"><?php echo isset($enSample['text']) ? (substr($enSample['text'], 0, 100) . (strlen($enSample['text']) > 100 ? '...' : '')) : 'No comment text'; ?></div>
                                        </div>
                                    <?php
                                    } catch (Exception $e) {
                                        echo '<p class="admin-note">' . ($editLang === 'ar' ? 'خطأ في عرض التعليق' : 'Error displaying comment') . '</p>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p class="admin-note"><?php echo $editLang === 'ar' ? 'لا توجد تعليقات حتى الآن' : 'No comments yet'; ?></p>
                    <?php endif; ?>
                </div>

                <p class="admin-note"><?php echo $editLang === 'ar' ? 'يمكنك إدارة التعليقات من' : 'You can manage comments from'; ?> <a href="comments.php"><?php echo $editLang === 'ar' ? 'صفحة التعليقات' : 'comments page'; ?></a>.</p>
            </div>

            <!-- Footer Section -->
            <div class="admin-form-section">
                <h3><?php echo $editLang === 'ar' ? 'قسم الفوتر' : 'Footer Section'; ?></h3>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="footer-logo"><?php echo $editLang === 'ar' ? 'شعار الفوتر' : 'Footer Logo'; ?></label>
                        <input type="text" id="footer-logo" name="footer_logo" value="<?php echo isset($langSettings['content']['footer']['logo']) ? $langSettings['content']['footer']['logo'] : ($editLang === 'ar' ? 'اربح ايفون 16' : 'Win iPhone 16'); ?>" required>
                    </div>

                    <div class="admin-form-col">
                        <label for="footer-tagline"><?php echo $editLang === 'ar' ? 'الشعار الفرعي' : 'Tagline'; ?></label>
                        <input type="text" id="footer-tagline" name="footer_tagline" value="<?php echo isset($langSettings['content']['footer']['tagline']) ? $langSettings['content']['footer']['tagline'] : ($editLang === 'ar' ? 'فرصتك للحصول على أحدث هاتف من آبل مجاناً' : 'Your chance to get the latest Apple phone for free'); ?>">
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="footer-copyright"><?php echo $editLang === 'ar' ? 'حقوق النشر' : 'Copyright'; ?></label>
                        <input type="text" id="footer-copyright" name="footer_copyright" value="<?php echo isset($langSettings['content']['footer']['copyright']) ? $langSettings['content']['footer']['copyright'] : ($editLang === 'ar' ? '© 2025 اربح ايفون 16. جميع الحقوق محفوظة.' : '© 2025 Win iPhone 16. All rights reserved.'); ?>" required>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-col">
                        <label for="footer-disclaimer1"><?php echo $editLang === 'ar' ? 'إخلاء المسؤولية' : 'Disclaimer'; ?></label>
                        <textarea id="footer-disclaimer1" name="footer_disclaimer1" rows="3"><?php echo isset($langSettings['content']['footer']['disclaimer1']) ? $langSettings['content']['footer']['disclaimer1'] : ($editLang === 'ar' ? 'هذا العرض محدود بالوقت وقد ينتهي في أي لحظة. العرض متاح لفترة محدودة فقط.' : 'This offer is time-limited and may end at any moment. The offer is available for a limited time only.'); ?></textarea>
                    </div>
                </div>

                <h4><?php echo $editLang === 'ar' ? 'روابط التواصل الاجتماعي' : 'Social Media Links'; ?></h4>
                <div id="social-container">
                    <?php if (isset($langSettings['content']['footer']['social'])): ?>
                        <?php foreach ($langSettings['content']['footer']['social'] as $index => $social): ?>
                            <div class="admin-social-item">
                                <div class="admin-form-row">
                                    <div class="admin-form-col">
                                        <label for="social-icon-<?php echo $index; ?>"><?php echo $editLang === 'ar' ? 'أيقونة' : 'Icon'; ?></label>
                                        <input type="text" id="social-icon-<?php echo $index; ?>" name="social[<?php echo $index; ?>][icon]" value="<?php echo $social['icon']; ?>" placeholder="<?php echo $editLang === 'ar' ? 'مثال: fab fa-facebook-f' : 'Example: fab fa-facebook-f'; ?>">
                                    </div>
                                    <div class="admin-form-col">
                                        <label for="social-url-<?php echo $index; ?>"><?php echo $editLang === 'ar' ? 'الرابط' : 'URL'; ?></label>
                                        <input type="text" id="social-url-<?php echo $index; ?>" name="social[<?php echo $index; ?>][url]" value="<?php echo $social['url']; ?>" placeholder="https://example.com">
                                    </div>
                                    <div class="admin-form-col admin-form-col-actions">
                                        <button type="button" class="admin-button admin-button-danger remove-social"><?php echo $editLang === 'ar' ? 'حذف' : 'Delete'; ?></button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" id="add-social" class="admin-button"><?php echo $editLang === 'ar' ? 'إضافة رابط تواصل اجتماعي' : 'Add Social Media Link'; ?></button>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-button admin-button-primary"><?php echo $editLang === 'ar' ? 'حفظ الإعدادات' : 'Save Settings'; ?></button>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>
