<?php

namespace DigitalMarketingFramework\Core\Model\Configuration;

use ArrayAccess;

/**
 * @extends ArrayAccess<string,mixed>
 */
interface ConfigurationInterface extends ArrayAccess
{
    public const KEY_VALUE_MAPS = 'valueMaps';

    public const KEY_IDENTIFIER = 'identifier';

    public const KEY_IDENTIFIER_COLLECTORS = 'collectors';

    public function isReadonly(): bool;

    /**
     * @param array<string,mixed> $configuration
     */
    public function addConfiguration(array $configuration): void;

    /**
     * @return array<string,mixed>
     */
    public function getRootConfiguration(): array;

    /**
     * @return array<array<string,mixed>>
     */
    public function toArray(): array;

    public static function convert(ConfigurationInterface $configuration, ?bool $readonly = null): static;

    public function get(string $key = '', mixed $default = null): mixed;

    public function set(string $key, mixed $value): void;

    public function unset(string $key): void;

    /**
     * @return ?array<string, array{uuid:string,weight:int,key:string,value:string}>
     */
    public function getValueMapConfiguration(string $id): ?array;

    /**
     * @return array<string,mixed>
     */
    public function getIdentifierCollectorConfiguration(string $identifierCollectorName): array;

    public function identifierCollectorExists(string $identifierCollectorName): bool;
}
