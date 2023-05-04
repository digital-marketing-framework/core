<?php

namespace DigitalMarketingFramework\Core\Model\Configuration;

use ArrayAccess;

interface ConfigurationInterface extends ArrayAccess
{
    public const KEY_VALUE_MAPS = 'valueMaps';

    public const KEY_IDENTIFIER = 'identifier';
    public const KEY_IDENTIFIER_COLLECTORS = 'collectors';

    public function isReadonly(): bool;
    public function addConfiguration(array $configuration): void;
    public function getRootConfiguration(): array;
    public function toArray(): array;
    public static function convert(ConfigurationInterface $configuration, ?bool $readonly = null): static;

    public function get(string $key = '', mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function unset(string $key): void;

    public function getValueMapConfiguration(string $key): ?array;

    public function getIdentifierCollectorConfiguration(string $identifierCollectorName): array;
    public function identifierCollectorExists(string $identifierCollectorName): bool;
}
