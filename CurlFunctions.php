<?php
class CurlFunctions {

    const USER_AGENT = "Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.99 Safari/537.36";

    public static function login($url, $username, $password) {
        $post = array('username' => $username, 'password' => $password);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_USERAGENT      => self::USER_AGENT,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $post,
            CURLOPT_HTTPHEADER     => array("Expect:  "),
        );

        $ch      = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        //$err     = curl_errno($ch);
        //$errmsg  = curl_error($ch);
        $header  = curl_getinfo($ch);
        curl_close($ch);

        //$header['errno']   = $err;
        //$header['errmsg']  = $errmsg;
        //$header['content'] = $content;

        preg_match('/^Set-Cookie:\s*([^;]*)/mi', $content, $match);
        parse_str($match[1], $cookies);
        return $cookies['PHPSESSID'];
    }

    public static function get($url, $sessionId) {

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_USERAGENT      => self::USER_AGENT,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => array("Expect:  "),
            CURLOPT_COOKIE         => 'PHPSESSID=' . $sessionId,
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);

        return($content);
    }
}


?>
