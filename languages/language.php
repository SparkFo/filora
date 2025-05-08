<?php
/**
 * Language management class
 */
class Language {
    private $translations = [];
    private $defaultLang = 'en';
    private $currentLang = 'en';
    private $availableLangs = ['ar', 'en'];

    /**
     * Constructor
     */
    public function __construct() {
        // Set language from session or cookie if available
        $this->initLanguage();

        // Load translations
        $this->loadTranslations();
    }

    /**
     * Initialize language from session, cookie, or browser settings
     */
    private function initLanguage() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Check if language is set in URL
        if (isset($_GET['lang']) && in_array($_GET['lang'], $this->availableLangs)) {
            $this->currentLang = $_GET['lang'];
            $_SESSION['lang'] = $this->currentLang;
            setcookie('lang', $this->currentLang, time() + (86400 * 30), "/"); // 30 days
        }
        // Check if language is set in session
        elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $this->availableLangs)) {
            $this->currentLang = $_SESSION['lang'];
        }
        // Check if language is set in cookie
        elseif (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $this->availableLangs)) {
            $this->currentLang = $_COOKIE['lang'];
            $_SESSION['lang'] = $this->currentLang;
        }
        // Try to detect from browser settings
        else {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);
            if (in_array($browserLang, $this->availableLangs)) {
                $this->currentLang = $browserLang;
            } else {
                $this->currentLang = $this->defaultLang;
            }
            $_SESSION['lang'] = $this->currentLang;
            setcookie('lang', $this->currentLang, time() + (86400 * 30), "/"); // 30 days
        }
    }

    /**
     * Load translations for current language
     */
    private function loadTranslations() {
        $langFile = __DIR__ . '/' . $this->currentLang . '.php';

        if (file_exists($langFile)) {
            $this->translations = require $langFile;
        } else {
            // Fallback to default language
            $defaultLangFile = __DIR__ . '/' . $this->defaultLang . '.php';
            if (file_exists($defaultLangFile)) {
                $this->translations = require $defaultLangFile;
            }
        }
    }

    /**
     * Get translation for a key
     *
     * @param string $key Translation key
     * @param array $params Parameters to replace in the translation
     * @return string Translated text
     */
    public function get($key, $params = []) {
        if (isset($this->translations[$key])) {
            $translation = $this->translations[$key];

            // Replace parameters
            if (!empty($params)) {
                foreach ($params as $param => $value) {
                    $translation = str_replace(':' . $param, $value, $translation);
                }
            }

            return $translation;
        }

        // Return the key if translation not found
        return $key;
    }

    /**
     * Get current language code
     *
     * @return string Language code
     */
    public function getCurrentLang() {
        return $this->currentLang;
    }

    /**
     * Get current language direction
     *
     * @return string Language direction (rtl or ltr)
     */
    public function getDirection() {
        return $this->get('lang_dir');
    }

    /**
     * Get current language name
     *
     * @return string Language name
     */
    public function getLanguageName() {
        return $this->get('lang_name');
    }

    /**
     * Get available languages
     *
     * @return array Available languages
     */
    public function getAvailableLanguages() {
        return $this->availableLangs;
    }

    /**
     * Get switch language URL
     *
     * @return string URL to switch language
     */
    public function getSwitchLanguageUrl() {
        $switchLang = ($this->currentLang == 'ar') ? 'en' : 'ar';

        // Get current URL
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        // Remove existing lang parameter
        $url = preg_replace('/(\?|&)lang=[^&]*/', '', $url);

        // Add new lang parameter
        $url .= (strpos($url, '?') === false) ? '?lang=' . $switchLang : '&lang=' . $switchLang;

        return $url;
    }
}

// Create global language instance
$lang = new Language();

/**
 * Helper function to get translation
 *
 * @param string $key Translation key
 * @param array $params Parameters to replace in the translation
 * @return string Translated text
 */
function __($key, $params = []) {
    global $lang;
    return $lang->get($key, $params);
}
