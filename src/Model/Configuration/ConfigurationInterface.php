<?php

namespace DigitalMarketingFramework\Core\Model\Configuration;

use ArrayAccess;

/**
 * @extends ArrayAccess<string,mixed>
 */
interface ConfigurationInterface extends ArrayAccess
{
    public const KEY_INTEGRATIONS = 'integrations';

    public const KEY_GENERAL_INTEGRATION = 'general';

    public const KEY_DATA_PROCESSING = 'dataProcessing';

    public const KEY_DATA_MAPPER_GROUPS = 'dataMapperGroups';

    public const KEY_CONDITIONS = 'conditions';

    public const KEY_VALUE_MAPS = 'valueMaps';

    public const KEY_IDENTIFIERS = 'identifiers';

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
     * @return array<string,mixed>
     */
    public function getDataProcessingConfiguration(): array;

    /**
     * @return ?array<string,mixed>
     */
    public function getDataMapperGroupConfiguration(string $id): ?array;

    /**
     * @return ?array<string,mixed>
     */
    public function getConditionConfiguration(string $id): ?array;

    /**
     * @return ?array<string, array{uuid:string,weight:int,key:string,value:string}>
     */
    public function getValueMapConfiguration(string $id): ?array;

    /**
     * @return array<string,mixed>
     */
    public function getGeneralIntegrationConfiguration(): array;

    public function integrationExists(string $integrationName): bool;

    /**
     * @return array<string,mixed>
     */
    public function getIntegrationConfiguration(string $integrationName): array;

    /**
     * @return array<string,array<string,mixed>>
     */
    public function getAllIntegrationConfigurations(): array;

    /**
     * @return array<string>
     */
    public function getAllIntegrationNames(): array;

    /**
     * @return array<string,mixed>
     */
    public function getIdentifierCollectorConfiguration(string $integrationName, string $identifierCollectorName): array;

    public function identifierCollectorExists(string $integrationName, string $identifierCollectorName): bool;
}
