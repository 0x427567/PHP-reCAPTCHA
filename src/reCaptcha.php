<?php
class reCaptcha {
    /**
     * @var $siteKey Google reCAPTCHA api site key
     * @var $secretKey Google reCAPTCHA api secret key
     * @var $verifyUrl Verify url
     * @var $apiUrl reCAPTCHA javascript url
     * @var $defaultLanguage reCAPTCHA language
     */
    private static $siteKey = '';
    private static $secretKey = '';
    private static $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?';
    private static $apiUrl = 'https://www.google.com/recaptcha/api.js';
    private $supportLanguages = [];
    private $defaultLanguage = '';

    /**
     * Initializes and check required properties
     */
    public function __construct() {
        if (self::$siteKey === null || self::$siteKey === '' || self::$secretKey === null || self::$secretKey === '')
            die('siteKey or secretKey can not be empty .');

        if (self::$verifyUrl === null || self::$verifyUrl === '')
            die('verifyUrl can not be empty .');

        if (self::$apiUrl === null || self::$apiUrl === '')
            die('apiUrl can not be empty .');
    }

    /**
     * Display reCAPTCHA
     *
     * Display html code on page
     */
    public function show() {
        $this->_generateCaptcha();
        $this->_generateApiUrl();
    }

    /**
     * Generate reCAPTCHA html code
     */
    private function _generateCaptcha() {
        echo '<div class="g-recaptcha" data-sitekey="' . self::$siteKey . '"></div>';
    }

    /**
     * Include reCAPTCHA javascript file
     */
    private function _generateApiUrl() {
        $this->_detectUserLanguage();
        echo '<script type="text/javascript" src="' . self::$apiUrl . '?hl=' . $this->defaultLanguage . '"></script>';
    }

	/**
	 * Detect user browser language
	 */
    private function _detectUserLanguage() {
        $locale = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        preg_match_all("/([a-z]{2})-?([a-z]{2})?/i", $locale, $languages);

        $this->_generateLanguageList();

        if (!$this->_setLanguage($languages))
            $this->defaultLanguage = 'en';
    }

	/**
	 * Setting reCAPTCHA display language
	 *
	 * @param array $language User browser languages
	 *
	 * @return true or false
	 */
    private function _setLanguage(array $language) {
        if (!is_array($language)) {
            $this->defaultLanguage = 'en';
            return true;
        }

        foreach ($language[0] as $key => $val) {
            if ($this->supportLanguages[$val]) {
                $this->defaultLanguage = $val;
                return true;
            } else if (isset($this->supportLanguages[$language[1][$key]])) {
                $this->defaultLanguage = $language[1][$key];
                return true;
            }
        }

        $this->defaultLanguage = 'en';
        return true;
    }

	/**
	 * Google reCAPTCHA support languages
	 */
    private function _generateLanguageList() {
        $this->supportLanguages = [
            'ar' => 1,
            'bg' => 1,
            'ca' => 1,
            'zh-CN' => 1,
            'zh-TW' => 1,
            'hr' => 1,
            'cs' => 1,
            'da' => 1,
            'nl' => 1,
            'en-GB' => 1,
            'en' => 1,
            'fil' => 1,
            'fi' => 1,
            'fr' => 1,
            'fr-CA' => 1,
            'de' => 1,
            'de-AT' => 1,
            'de-CH' => 1,
            'el' => 1,
            'iw' => 1,
            'hi' => 1,
            'hu' => 1,
            'id' => 1,
            'it' => 1,
            'ja' => 1,
            'ko' => 1,
            'lv' => 1,
            'lt' => 1,
            'no' => 1,
            'fa' => 1,
            'pl' => 1,
            'pt' => 1,
            'pt-BR' => 1,
            'pt-PT' => 1,
            'ro' => 1,
            'ru' => 1,
            'sr' => 1,
            'sk' => 1,
            'sl' => 1,
            'es' => 1,
            'es-419' => 1,
            'sv' => 1,
            'th' => 1,
            'tr' => 1,
            'uk' => 1,
            'vi' => 1,
        ];
    }

    /**
     * Verify client is robot or not
     *
     * @param string $response Google verify response
     * @param string $remoteIp Client IP address
     *
     * @return string Verified result and JSON format
     */
    public function verify($response, $remoteIp = null) {
        return $this->_getResult(array(
            'secret' => self::$secretKey,
            'remoteip' => ($remoteIp) ? $remoteIp : $_SERVER['REMOTE_ADDR'],
            'response' => $response,
        ));
    }

    /**
     * Get reCAPTCHA verify result
     *
     * @param array $query query fields
     *
     * @return string Verify result
     */
    private function _getResult($query) {
        if (!isset($query['response'])) {
            return array(
                'success' => false,
                'error-codes' => 'missing-input',
            );
        }

        $request = $this->_encode($query);

        return file_get_contents(self::$verifyUrl . $request);
    }

    /**
     * Build http get query string
     *
     * @param array $data query fields
     *
     * @return string HTTP GET query string
     */
    private function _encode($data = array()) {
        return http_build_query($data);
    }
}
