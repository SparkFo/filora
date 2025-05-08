<?php
// Get offer link from settings
$offerLink = $settings['general']['offerLink'];
?>

<section id="cta-section" class="cta-section">
    <div class="container">
        <h2 class="section-title"><?php echo isset($langSettings['content']['cta']['title']) ? $langSettings['content']['cta']['title'] : __('cta_title'); ?></h2>
        <div class="cta-container">
            <a href="<?php echo $offerLink; ?>" target="_blank" class="cta-button-rect large"><?php echo isset($langSettings['content']['cta']['buttonText']) ? $langSettings['content']['cta']['buttonText'] : __('cta_button'); ?></a>
            <div class="countdown-container centered">
                <h3><?php echo isset($langSettings['content']['cta']['countdownText']) ? $langSettings['content']['cta']['countdownText'] : __('cta_countdown'); ?></h3>
                <div id="countdown-cta" class="countdown">
                    <div class="time-block">
                        <span id="days-cta">00</span>
                        <span class="time-label"><?php echo __('time_days'); ?></span>
                    </div>
                    <div class="time-block">
                        <span id="hours-cta">00</span>
                        <span class="time-label"><?php echo __('time_hours'); ?></span>
                    </div>
                    <div class="time-block">
                        <span id="minutes-cta">00</span>
                        <span class="time-label"><?php echo __('time_minutes'); ?></span>
                    </div>
                    <div class="time-block">
                        <span id="seconds-cta">00</span>
                        <span class="time-label"><?php echo __('time_seconds'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
