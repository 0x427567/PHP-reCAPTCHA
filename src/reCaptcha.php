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
    private $defaultLanguage = 'en';

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

        if (!isset($defaultLanguage))
            $this->defaultLanguage = 'en';
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
        echo '<script type="text/javascript" src="' . self::$apiUrl . '?hl=' . $this->defaultLanguage . '"></script>';
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