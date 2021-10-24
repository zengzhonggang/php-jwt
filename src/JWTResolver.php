<?php


namespace ZZG\JWT;


use UnexpectedValueException;
use ZZG\JWT\Algorithm\Signature;
use ZZG\JWT\Base64\UrlSafeBase64;
use ZZG\JWT\Exception\BeforeValidException;
use ZZG\JWT\Exception\SignatureInvalidException;
use ZZG\JWT\Exception\TokenInvalidException;
use ZZG\JWT\Header\Header;
use ZZG\JWT\Payload\Claim;

class JWTResolver
{
    private $token;
    private $signatureString;

    /**
     * @return mixed
     */
    private function getSignatureString()
    {
        return $this->signatureString;
    }

    /**
     * @param mixed $signatureString
     */
    private function setSignatureString($signatureString)
    {
        $this->signatureString = $signatureString;
    }
    private $keys;

    /**
     * @return JWTKey[]
     */
    private function getKeys()
    {
        return $this->keys;
    }

    /**
     * @param JWTKey[] $keys
     */
    private function setKeys($keys)
    {
        $this->keys = $keys;
    }
    /**
     * @var Header
     */
    private $header;

    /**
     * @return Header
     */
    public function getHeader()
    {
        if (!$this->header) {
            $this->parseTokenString($this->token);
        }
        return $this->header;
    }

    /**
     * @param Header $header
     */
    private function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return Claim
     */
    public function getPayload()
    {
        if (!$this->payload) {
            $this->parseTokenString($this->token);
        }
        return $this->payload;
    }

    /**
     * @param Claim $payload
     */
    private function setPayload($payload)
    {
        $this->payload = $payload;
    }
    /**
     * @var Claim
     */
    private $payload;
    /**
     * JWTResolver constructor.
     * @param string $token
     * @param JWTKey[] $keys
     */
    public function __construct($token,$keys)
    {
        $this->setKeys($keys);
        $this->token = $token;
    }

    /**
     * 验证 token有效性
     * @return bool
     */
    public function verify()
    {
        $sign = new Signature($this->getKeys()[$this->getHeader()->get('kid')]);
        if ($this->getSignatureString() !== $sign->sign($this->getPayload(),$this->getHeader())){
            throw new SignatureInvalidException('签名验证失败');
        }
        if ($this->getPayload()->getNotBefore() > time()) {
            throw new BeforeValidException('jwt还未生效');
        }
        if ($this->getPayload()->getExpirationTime() != -1 && $this->getPayload()->getExpirationTime() <= time()) {
            throw new SignatureInvalidException('jwt已过期');
        }
        return true;
    }

    /**
     * 返回token解析结果code
     * @return int|mixed
     */
    public function errorCode()
    {
        $code = 0;
        try {
            $this->verify();
        } catch (UnexpectedValueException $exception) {
            $code = $exception->getCode();
        }
        return $code;
    }
    private function parseTokenString($tokenString)
    {
        $tokenArray = explode('.',$tokenString);
        if (count($tokenArray) != 3 ) {
            throw new TokenInvalidException('token格式错误');
        }
        $headerArray = UrlSafeBase64::decode($tokenArray[0]);
        $payloadArray = UrlSafeBase64::decode($tokenArray[1]);
        $this->setSignatureString($tokenArray[2]);
        $this->setHeader(new Header($headerArray));
        $this->setPayload(new Claim($payloadArray));
    }
}