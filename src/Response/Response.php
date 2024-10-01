<?php

declare(strict_types=1);

namespace Ssanko\Compari\Response;

use Ssanko\Compari\Config\Constant;

final class Response
{
    public static function create(string $apiKey, string $query): string
    {
        $random = md5($apiKey . microtime());

        // Sending:
        $output = '<script type="text/javascript">window.aku_request_done = function(w, c) {';
        $output .= 'var I = new Image(); I.src="' . Constant::SERVICE_URL_SEND . Constant::SERVICE_TOKEN_PROCESS . $query . '" + c;';
        $output .= '};</script>';
        // Include:
        $output .= '<script type="text/javascript"> (function() {';
        $output .= 'var a=document.createElement("script"); a.type="text/javascript"; a.src="' . Constant::SERVICE_URL_AKU . '"; a.async=true;';
        $output .= '(document.getElementsByTagName("head")[0]||document.getElementsByTagName("body")[0]).appendChild(a);';
        $output .= '})();</script>';
        // Fallback:
        $output .= '<noscript>';
        $output .= '<img src="' . Constant::SERVICE_URL_SEND . Constant::SERVICE_TOKEN_PROCESS . $query . $random . '" />';
        $output .= '</noscript>';

        return $output;
    }
}
