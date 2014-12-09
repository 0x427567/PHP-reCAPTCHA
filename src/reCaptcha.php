<?php
class reCaptcha {
    private static $siteKey = '';
    private static $secretKey = '';
    private static $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?';
    private static $apiUrl = 'https://www.google.com/recaptcha/api.js';
    private $defaultLanguage = 'en';

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

    public function show() {
        $this->_generateCaptcha();
	    $this->_generateApiUrl();
    }

    private function _generateCaptcha() {
        echo '<div class="g-recaptcha" data-sitekey="' . self::$siteKey . '"></div>';
    }

    private function _generateApiUrl() {
        echo '<script type="text/javascript" src="' . self::$apiUrl . '?hl=' . $this->defaultLanguage . '"></script>';
    }

    public function verify($response, $remoteIp = null) {
        return $this->_getResult(array(
            'secret' => self::$secretKey,
            'remoteip' => ($remoteIp) ? $remoteIp : $_SERVER['REMOTE_ADDR'],
            'response' => $response,
        ));
    }

    private function _encode($data = array()) {
        return http_build_query($data);
    }

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
}