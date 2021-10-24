<?php


namespace ZZG\JWT\Exception;


class TokenInvalidException extends \UnexpectedValueException
{
    const CODE = '10001';
    protected $code = self::CODE;

}