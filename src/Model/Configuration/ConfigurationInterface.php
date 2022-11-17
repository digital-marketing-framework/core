<?php

namespace DigitalMarketingFramework\Core\Model\Configuration;

use ArrayAccess;

interface ConfigurationInterface extends ArrayAccess
{
    const KEY_SELF = 'self';

    public function addConfiguration(array $configuration);
    public function toArray(): array;

    public function get(string $key = '', mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function unset(string $key): void;
}
