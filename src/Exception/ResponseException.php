<?php

namespace Ssanko\Compari\Exception;

class ResponseException extends CompariException
{
    public const ERROR_JSON_ERROR         = 1;
    public const ERROR_REQUEST_TIMED_OUT  = 2;
    public const ERROR_REQUEST_FAILED     = 3;
    public const ERROR_BAD_REQUEST        = 4;
    public const TRUSTED_SHOP_NOT_ENABLED = 5;

    private function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }

    public static function jsonError(string $error): self
    {
        return new self("Json error: $error", self::ERROR_JSON_ERROR);
    }

    public static function requestTimedOut(): self
    {
        return new self('Request timed out.', self::ERROR_REQUEST_TIMED_OUT);
    }

    public static function requestFailed(?string $reason = null): self
    {
        return new self(
            $reason === null
                ? 'Request failed.'
                : "Request failed because of $reason",
            self::ERROR_REQUEST_FAILED
        );
    }

    public static function badRequest(int|string $errorCode, string $errorMessage): self
    {
        return new self("Bad request: $errorCode - $errorMessage", self::ERROR_BAD_REQUEST);
    }

    public static function trustedShopNotEnabled(): self
    {
        return new self("TrustedShop program not enabled", self::TRUSTED_SHOP_NOT_ENABLED);

    }
}
