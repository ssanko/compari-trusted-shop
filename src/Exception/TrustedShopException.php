<?php

namespace Ssanko\Compari\Exception;

class TrustedShopException extends CompariException
{
    public const ERROR_EMPTY_EMAIL     = 1;
    public const ERROR_EMPTY_WEBAPIKEY = 2;
    public const ERROR_EXAMPLE_EMAIL   = 3;
    public const ERROR_EXAMPLE_PRODUCT = 4;

    private function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }

    public static function emptyEmail(): self
    {
        return new self('Customer e-mail address is empty.', self::ERROR_EMPTY_EMAIL);
    }

    public static function emptyWebApiKey(): self
    {
        return new self('Partner WebApiKey is empty.', self::ERROR_EMPTY_WEBAPIKEY);
    }

    public static function exampleEmail(): self
    {
        return new self('Customer e-mail address has been not changed yet.', self::ERROR_EXAMPLE_EMAIL);
    }

    public static function emptyProduct(): self
    {
        return new self('Product name is empty.', self::ERROR_EXAMPLE_PRODUCT);
    }
}
