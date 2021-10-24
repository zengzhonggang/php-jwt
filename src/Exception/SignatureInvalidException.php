<?php


namespace ZZG\JWT\Exception;


class SignatureInvalidException extends \UnexpectedValueException
{
    const CODE = '10002';
    protected $code = self::CODE;

}