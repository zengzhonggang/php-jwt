<?php


namespace ZZG;


use UnexpectedValueException;
use ZZG\Algorithm\Signature;
use ZZG\Base64Url\Base64Url;
use ZZG\Exception\BeforeValidException;
use ZZG\Exception\SignatureInvalidException;
use ZZG\Header\Header;
use ZZG\Payload\Claim;

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
        $tokenArray = explode('.',$token);
        if (count($tokenArray) != 3 ) {
            throw new UnexpectedValueException('token格式错误');
        }
        $headerArray = Base64Url::decode($tokenArray[0]);
        $payloadArray = Base64Url::decode($tokenArray[1]);
        $this->setSignatureString($tokenArray[2]);
        $this->setHeader(new Header($headerArray));
        $this->setPayload(new Claim($payloadArray));
        $this->setKeys($keys);
        $this->token = $token;
    }
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
}