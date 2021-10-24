<?php


namespace ZZG\JWT\Exception;


class ExpiredException extends \UnexpectedValueException
{
    const CODE = '10004';
    protected $code = self::CODE;
}