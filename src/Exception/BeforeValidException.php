<?php


namespace ZZG\JWT\Exception;


class BeforeValidException extends \UnexpectedValueException
{
    const CODE = '10003';
    protected $code = self::CODE;
}