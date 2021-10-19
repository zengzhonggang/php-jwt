<?php


namespace ZZG;


use ZZG\Algorithm\Signature;
use ZZG\Base64\UrlSafeBase64;
use ZZG\Header\Header;
use ZZG\Payload\Claim;

class JWTBuilder
{
    private $kid = null;
    /**
     * @var JWTKey[]
     */
    private $keys = [];
    /**
     * @var Claim
     */
    private $payload;
    private $header;

    /**
     * JWTBuilder constructor.
     * @param Claim | array $payload
     * @param $keys
     */
    public function __construct($payload,$keys)
    {
        if (is_array($payload)) {
            $this->payload = new Claim($payload);
        } else {
            $this->payload = $payload;
        }
        $this->keys = $keys;
        $this->header = new Header();
    }
    public function setPayload($name, $value) {
        $this->payload->setPublicClaim($name,$value);
    }
    public function setHeader($name, $value) {
        $this->header->set($name,$value);
        return $this;
    }
    public function setKid($kid,$forceCover = false) {
        if ($this->kid !== null && $forceCover === false) {
            throw new \UnexpectedValueException('已设定key值');
        }
        if (empty($this->keys[$kid])) {
            throw new \UnexpectedValueException('key不存在');
        }
        $this->kid = $kid;
        return $this;
    }
    public function setKindex($kindex,$forceCover = false) {
        if ($this->kid !== null && $forceCover === false) {
            throw new \UnexpectedValueException('已设定key值');
        }
        $kids = array_keys($this->keys);
        if (empty($kids[$kindex])) {
            throw new \UnexpectedValueException('key不存在');
        }
        $this->kid = $kids[$kindex];
        return $this;
    }
    public function toString()
    {
        $key = $this->keys[$this->kid];
        $this->header->setAlg($key->getAlg());
        $this->header->set('kid',$key->getKid());
        $sign = new Signature($key);
        return UrlSafeBase64::encode($this->header->toArray()).'.'.UrlSafeBase64::encode($this->payload->toArray()).'.'.$sign->sign($this->payload,$this->header);
    }
    public function __toString()
    {
        return $this->toString();
    }
}