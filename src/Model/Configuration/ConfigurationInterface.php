<?php

namespace DigitalMarketingFramework\Core\Model\Configuration;

use ArrayAccess;

interface ConfigurationInterface extends ArrayAccess
{
    const KEY_SELF = 'self';

    public const KEY_DATA_MAPS = 'dataMaps';
    public const DEFAULT_DATA_MAPS = [];

    public function addConfiguration(array $configuration): void;
    public function toArray(): array;

    public function get(string $key = '', mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function unset(string $key): void;

    public function getDataMapConfiguration(string $key): ?array;
}
