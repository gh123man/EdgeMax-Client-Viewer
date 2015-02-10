<?php


$options = array(
    CURLOPT_RETURNTRANSFER => true,     // return web page
    CURLOPT_HEADER         => false,    // don't return headers
    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
    CURLOPT_ENCODING       => "",       // handle all encodings
    CURLOPT_USERAGENT      => "Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.99 Safari/537.36", // who am i
    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
    CURLOPT_TIMEOUT        => 120,      // timeout on response
    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER     => array("Expect:  "),
    CURLOPT_COOKIE         => 'PHPSESSID=loe23v3lgedanpie4ecomfq2ej5bk1aq',
);

$ch      = curl_init( 'https://router/api' );
curl_setopt_array( $ch, $options );
$content = curl_exec( $ch );
$err     = curl_errno( $ch );
$errmsg  = curl_error( $ch );
$header  = curl_getinfo( $ch );
curl_close( $ch );

$header['errno']   = $err;
$header['errmsg']  = $errmsg;
$header['content'] = $content;
print_r($content);

?>
