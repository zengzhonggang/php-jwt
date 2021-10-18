<?php


namespace ZZG;


class JWTKey
{
    public function __construct($alg,$key,$kid = null)
    {
        $this->setAlg($alg);
        $this->setKey($key);
        if ($kid) {
            $this->setKid($kid);
        } else {
            $this->setKid($this->generateDefaultKid());
        }
    }

    private $kid;

    /**
     * @param mixed $kid
     */
    public function setKid($kid)
    {
        $this->kid = $kid;
    }
    private $alg;

    /**
     * @param mixed $alg
     */
    public function setAlg($alg)
    {
        $this->alg = $alg;
    }
    private $key;

    /**
     * @param  $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
    public function getKid()
    {
        return $this->kid;
    }

    public function getAlg()
    {
        return $this->alg;
    }

    public function getSignKey()
    {
        return is_array($this->key)?$this->key[1]:$this->key;
    }

    public function getPublicKey()
    {
        return is_array($this->key)?$this->key[0]:$this->key;
    }

    public function getPrivateKey()
    {
        return is_array($this->key)?$this->key[1]:$this->key;
    }

    /**
     * 根据md5生成一个16位id
     * @return false|string
     */
    private function generateDefaultKid()
    {
        if (is_array($this->key)) {
            $keyValue = implode('',$this->key);
        } else {
            $keyValue = $this->key;
        }
        return substr(md5($keyValue.$this->getAlg()),8,16);
    }

}