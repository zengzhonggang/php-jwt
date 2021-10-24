<?php
namespace ZZG\JWT;

use ZZG\JWT\Algorithm\Signature;
use ZZG\JWT\Payload\Claim;

class JWT
{
    const HS256 = 'HS256';
    const HS384 = 'HS384';
    const HS512 = 'HS512';
    const RS256 = 'RS256';
    const RS384 = 'RS384';
    const RS512 = 'RS512';
    private static $instance;
    /**
     * @var JWTKey[]
     */
    private $keys = [];
    /**
     * @param array $keys array(算法, key, kid(可选) ) 或者 array( array(算法, key, kid(可选)) ) ;当算法是rsa算法时，key 为数组[privateKey,publicKey];
     * kid默认值为MD5算法生成，有碰撞概率，建议自定义.
     */
    public function __construct(array $keys)
    {
        $this->keys = self::parseKeys($keys);
    }
    /**
     * @param array $keys array(算法, key, kid(可选) ) 或者 array( array(算法, key, kid(可选)) ) ;当算法是rsa算法时，key 为数组[privateKey,publicKey];
     * kid默认值为MD5算法生成，有碰撞概率，建议自定义.
     * @return JWT
     */
    public static function init(array $keys)
    {
        if (!(self::$instance instanceof static) ) {
            self::$instance = new static($keys);
        }
        return self::$instance;
    }

    /**
     * token 生成器
     * @param array | Claim $payload
     * @return JWTBuilder
     */
    public  function generateToken($payload)
    {
        return new JWTBuilder($payload,$this->keys);
    }

    /**
     * token 解析器
     * @param string $token
     * @return JWTResolver
     */
    public  function analyticToken(string $token)
    {
        return new JWTResolver($token,$this->keys);
    }

    private function parseKeys(array $keys)
    {
        $k = [];
        if (is_array($keys[array_keys($keys)[0]])) {
            foreach ($keys as $key) {
                $parseKey = self::parseKey($key);
                $k[$parseKey->getKid()] = $parseKey;
            }
        } else {
            $parseKey = self::parseKey($keys);
            $k[$parseKey->getKid()] = $parseKey;
        }
        return $k;
    }
    private function parseKey(array $key)
    {
        $key = array_values($key);
        $alg = empty($key[0])?'':$key[0];
        Signature::matchAlg($alg);
        if (empty($key[1])) {
            throw new \UnexpectedValueException('key值不能为空');
        }
        $keyValue = $key[1];
        if (Algorithm\Signature::matchAlg($alg)[0] === 'hash_hmac' &&  !is_string($keyValue)) {
            throw new \UnexpectedValueException($alg.'算法key值只能是字符串');
        } elseif (Algorithm\Signature::matchAlg($alg)[0] === 'openssl' &&  !is_array($keyValue)) {
            throw new \UnexpectedValueException($alg.'算法key值只能是数组');
        }
        if (empty($key[2])) {
            return new JWTKey($alg,$keyValue);
        } else {
            return new JWTKey($alg,$keyValue,$key[2]);
        }

    }
}