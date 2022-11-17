<?php

namespace DigitalMarketingFramework\Core\Context;

use ArrayAccess;

interface ContextInterface extends ArrayAccess
{
    public const KEY_IP_ADDRESS = 'ip_address';
    public const KEY_TIMESTAMP = 'timestamp';
    public const KEY_COOKIES = 'cookies';
    public const KEY_REQUEST_VARIABLES = 'request_variables';

    public function toArray(): array;

    public function getCookies(): array;
    public function getCookie(string $name): ?string;
    public function getIpAddress(): ?string;
    public function getTimestamp(): ?int;
    public function getRequestVariables(): array;
    public function getRequestVariable(string $name): ?string;
}
