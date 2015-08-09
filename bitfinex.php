<?php
/**
 * @param string $path
 * @param array $req
 * @throws Exception
 * @return mixed
 */
function bitfinex_query($path, array $req = Array())
{
    global $config;
    // API settings, add your Key and Secret at here

    $key = "sdsdsd";
    $secret = "dfdfsfererw";

    // generate a nonce to avoid problems with 32bits systems
    $mt = explode(' ', microtime());
    $req['request'] = "/v1".$path;
    $req['nonce'] = $mt[1].substr($mt[0], 2, 6);

    // generate the POST data string
    $post_data = base64_encode(json_encode($req));

    $sign = hash_hmac('sha384', $post_data, $secret);

    // generate the extra headers
    $headers = array(
        'X-BFX-APIKEY: '.$key,
        'X-BFX-PAYLOAD: '.$post_data,
        'X-BFX-SIGNATURE: '.$sign,
    );

    // curl handle (initialize if required)
    static $ch = null;
    if (is_null($ch)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/4.0 (compatible; Bter PHP bot; '.php_uname('a').'; PHP/'.phpversion().')'
        );
    }

    curl_setopt($ch, CURLOPT_URL, 'https://api.bitfinex.com/v1'.$path);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    // run the query
    $res = curl_exec($ch);

    if ($res === false) throw new Exception('Curl error: '.curl_error($ch));
    $dec = json_decode($res, true);
    if (!$dec) throw new Exception('Invalid data: '.$res);
    return $dec;
}