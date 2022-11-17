<?php

namespace DigitalMarketingFramework\Core\Request;

class DefaultRequest implements RequestInterface
{
    public function getCookies(): array
    {
        return $_COOKIE;
    }

    public function getIpAddress(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function getRequestVariable(string $name): string
    {
        return $_SERVER[$name] ?? '';
    }
}
