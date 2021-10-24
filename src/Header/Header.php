<?php
/**
 * Created by PhpStorm.
 * User: zzg
 * Date: 2020-07-19
 * Time: 16:40
 */

namespace ZZG\JWT\Header;


class Header
{
    const TYPE = 'type';
    const ALG = 'alg';
    private $_object;

    public function __construct(array $data = [])
    {
        $this->_object = new \stdClass();
        $this->setType('JWT');
        if (!empty($data)) {
            foreach ($data as $key=>$value) {
                $this->set($key,$value);
            }
        }
    }

    public function setType($type) {
        return $this->set(self::TYPE,$type);
    }
    public function setAlg($alg) {
        return $this->set(self::ALG,$alg);
    }
    public function set($key,$value) {
        $this->_object->{$key} = $value;
        return $this;
    }
    public function get($key)
    {
        return property_exists($this->_object,$key)?$this->_object->{$key}:null;
    }
    public function toArray(){
        return (array)$this->_object;
    }
}