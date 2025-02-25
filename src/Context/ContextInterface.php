<?php

namespace DigitalMarketingFramework\Core\Context;

use ArrayAccess;

/**
 * @extends ArrayAccess<string,mixed>
 */
interface ContextInterface extends ArrayAccess
{
    public const KEY_IP_ADDRESS = 'ip_address';

    public const KEY_HOST = 'host';

    public const KEY_URI = 'uri';

    public const KEY_REFERER = 'referer';

    public const KEY_TIMESTAMP = 'timestamp';

    public const KEY_COOKIES = 'cookies';

    public const KEY_REQUEST_VARIABLES = 'request_variables';

    public const KEY_REQUEST_ARGUMENTS = 'request_arguments';

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

    public function getHost(): ?string;

    public function getUri(): ?string;

    public function getReferer(): ?string;

    public function getTimestamp(): ?int;

    /**
     * @return array<string,string>
     */
    public function getRequestVariables(): array;

    public function getRequestVariable(string $name): ?string;

    /**
     * @return array<string,mixed>
     */
    public function getRequestArguments(): array;

    public function getRequestArgument(string $name): mixed;

    public function isResponsive(): bool;

    public function setResponseCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = true,
        bool $httponly = true,
    ): void;

    /**
     * @return array<string,mixed>
     */
    public function getResponseData(): array;

    public function applyResponseData(): void;
}
