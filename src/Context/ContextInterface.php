<?php

namespace DigitalMarketingFramework\Core\Context;

use ArrayAccess;

/**
 * @extends ArrayAccess<string,mixed>
 */
interface ContextInterface extends ArrayAccess
{
    public const KEY_IP_ADDRESS = 'ip_address';

    public const KEY_TIMESTAMP = 'timestamp';

    public const KEY_COOKIES = 'cookies';

    public const KEY_REQUEST_VARIABLES = 'request_variables';

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array;

    /**
     * @return array<string,string>
     */
    public function getCookies(): array;

    public function getCookie(string $name): ?string;

    public function getIpAddress(): ?string;

    public function getTimestamp(): ?int;

    /**
     * @return array<string,string>
     */
    public function getRequestVariables(): array;

    public function getRequestVariable(string $name): ?string;
}
