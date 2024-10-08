<?php

declare(strict_types=1);

namespace Ssanko\Compari\Config;

class ArukeresoConfig
{
    public function getApiVersion(): string
    {
        return '2.0/PHP';
    }

    public function getServiceUrlSend(): string
    {
        return 'https://www.arukereso.hu/';
    }

    public function getServiceUrlAku(): string
    {
        return 'https://assets.arukereso.com/aku.min.js';
    }

    public function getServiceTokenRequest(): string
    {
        return 't2/TokenRequest.php';
    }

    public function getServiceTokenProcess(): string
    {
        return 't2/TrustedShop.php';
    }
}
