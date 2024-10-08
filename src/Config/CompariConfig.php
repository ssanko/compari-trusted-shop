<?php

declare(strict_types=1);

namespace Ssanko\Compari\Config;

class CompariConfig extends ArukeresoConfig
{
    public function getServiceUrlSend(): string
    {
        return 'https://www.compari.ro/';
    }
}
