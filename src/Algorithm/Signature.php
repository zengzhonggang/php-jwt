<?php


namespace ZZG\JWT\Algorithm;


use DomainException;
use ZZG\JWT\Base64\UrlSafeBase64;
use ZZG\JWT\Header\Header;
use ZZG\JWT\JWTKey;
use ZZG\JWT\Payload\Claim;

class Signature
{
    private static $algs = array(
        'HS256' => array('hash_hmac', 'SHA256'),
        'HS384' => array('hash_hmac', 'SHA384'),
        'HS512' => array('hash_hmac', 'SHA512'),
        'RS256' => array('openssl', 'SHA256'),
        'RS384' => array('openssl', 'SHA384'),
        'RS512' => array('openssl', 'SHA512'),
    );

    private $key;

    /**
     * @return JWTKey
     */
    private function getKey()
    {
        return $this->key;
    }

    /**
     * @param JWTKey $key
     */
    private function setKey($key)
    {
        $this->key = $key;
    }
    public function __construct(JWTKey $key)
    {
        $this->setKey($key);
    }

    /**
     * @param Claim $payload
     * @param Header $header
     */
    public function sign($payload,$header)
    {
        $string = UrlSafeBase64::encode($header->toArray()).UrlSafeBase64::encode($payload->toArray());
        list($function, $algorithm) = static::matchAlg($this->getKey()->getAlg());
        $signatureString = false;
        switch ($function) {
            case 'hash_hmac':
                $signatureString =  hash_hmac($algorithm, $string, $this->getKey()->getSignKey());
                break;
            case 'openssl':
                $signature = '';
                $success = openssl_sign($string, $signature, $this->getKey()->getSignKey(), $algorithm);
                if (!$success) {
                    throw new DomainException("OpenSSL 算法签名失败");
                }
                $signatureString = $signature;
        }
        return UrlSafeBase64::encode($signatureString);
    }

    public static function matchAlg($alg)
    {
        $alg = strtoupper($alg);
        if (isset(self::$algs[$alg])) {
            return self::$algs[$alg];
        } else {
            throw new \UnexpectedValueException('不支持该算法');
        }

    }
}