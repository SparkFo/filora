<?php
// Define footer links
$links = [
    ['text' => __('nav_about'), 'url' => '#about'],
    ['text' => __('nav_get_it'), 'url' => '#cta-section'],
    ['text' => __('nav_comments'), 'url' => '#comments']
];

// Define social links
if (isset($langSettings['content']['footer']['social']) && !empty($langSettings['content']['footer']['social'])) {
    // Use social links from settings
    $social = $langSettings['content']['footer']['social'];
} else {
    // Fallback to default social links
    $social = [
        ['icon' => 'fab fa-facebook-f', 'url' => '#'],
        ['icon' => 'fab fa-twitter', 'url' => '#'],
        ['icon' => 'fab fa-instagram', 'url' => '#'],
        ['icon' => 'fab fa-youtube', 'url' => '#']
    ];
}
?>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <h2><?php echo isset($langSettings['content']['footer']['logo']) ? $langSettings['content']['footer']['logo'] : __('footer_logo'); ?></h2>
                <p><?php echo isset($langSettings['content']['footer']['tagline']) ? $langSettings['content']['footer']['tagline'] : __('footer_tagline'); ?></p>
            </div>
            <div class="footer-links">
                <h3><?php echo __('footer_links'); ?></h3>
                <ul>
                    <?php foreach ($links as $link): ?>
                    <li><a href="<?php echo $link['url']; ?>"><?php echo $link['text']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="footer-social">
                <h3><?php echo __('footer_follow'); ?></h3>
                <div class="social-icons">
                    <?php foreach ($social as $socialLink): ?>
                    <a href="<?php echo $socialLink['url']; ?>" target="_blank"><i class="<?php echo $socialLink['icon']; ?>"></i></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p><?php echo isset($langSettings['content']['footer']['copyright']) ? $langSettings['content']['footer']['copyright'] : __('footer_copyright'); ?></p>
            <p><?php echo isset($langSettings['content']['footer']['disclaimer1']) ? $langSettings['content']['footer']['disclaimer1'] : __('footer_disclaimer'); ?></p>
            <?php if ($isAdmin): ?>
            <p class="admin-login-link"><a href="admin/login.php"><?php echo __('footer_admin'); ?></a></p>
            <?php endif; ?>
        </div>
    </div>
</footer>
