<?php
// Define features
if (isset($langSettings['content']['about']['features']) && !empty($langSettings['content']['about']['features'])) {
    // Use features from settings
    $features = $langSettings['content']['about']['features'];
} else {
    // Fallback to default features
    $features = [
        [
            'icon' => 'fas fa-mobile-alt',
            'title' => __('feature_1_title'),
            'description' => __('feature_1_desc')
        ],
        [
            'icon' => 'fas fa-camera',
            'title' => __('feature_2_title'),
            'description' => __('feature_2_desc')
        ],
        [
            'icon' => 'fas fa-battery-full',
            'title' => __('feature_3_title'),
            'description' => __('feature_3_desc')
        ]
    ];
}
?>

<section id="about" class="about-section">
    <div class="container">
        <h2 class="section-title"><?php echo isset($langSettings['content']['about']['title']) ? $langSettings['content']['about']['title'] : __('about_title'); ?></h2>
        <div class="about-content">
            <div class="about-text">
                <p><?php echo isset($langSettings['content']['about']['paragraph1']) ? $langSettings['content']['about']['paragraph1'] : __('about_p1'); ?></p>
                <p><?php echo isset($langSettings['content']['about']['paragraph2']) ? $langSettings['content']['about']['paragraph2'] : __('about_p2'); ?></p>
                <p><?php echo isset($langSettings['content']['about']['paragraph3']) ? $langSettings['content']['about']['paragraph3'] : __('about_p3'); ?></p>
                <div class="features">
                    <?php foreach ($features as $feature): ?>
                    <div class="feature">
                        <i class="<?php echo $feature['icon']; ?>"></i>
                        <h3><?php echo $feature['title']; ?></h3>
                        <p><?php echo $feature['description']; ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
