<?php

namespace DigitalMarketingFramework\Core\Model\Configuration;

use BadMethodCallException;
use DigitalMarketingFramework\Core\Utility\ConfigurationUtility;
use DigitalMarketingFramework\Core\Utility\MapUtility;

class Configuration implements ConfigurationInterface
{
    /** @var ?array<string,mixed> */
    protected ?array $manualOverride = null;

    /**
     * @param array<array<string,mixed>> $configurationList
     */
    final public function __construct(
        protected array $configurationList,
        protected bool $readonly = true,
    ) {
    }

    public static function convert(ConfigurationInterface $configuration, ?bool $readonly = null): static
    {
        return new static($configuration->toArray(), $readonly ?? $configuration->isReadonly());
    }

    protected function readonlyCheck(string $action): void
    {
        if ($this->readonly) {
            throw new BadMethodCallException(sprintf('configuration is readonly, action "%s" is not allowed', $action));
        }
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function addConfiguration(array $configuration): void
    {
        $this->readonlyCheck('addConfiguration');
        $this->configurationList[] = $configuration;
        unset($this->manualOverride);
        $this->manualOverride = null;
    }

    public function getRootConfiguration(): array
    {
        $asArray = $this->toArray();
        if ($asArray === []) {
            return [];
        }

        return $asArray[count($asArray) - 1];
    }

    public function toArray(): array
    {
        return $this->configurationList;
    }

    /**
     * @return array<string,mixed>
     */
    protected function getMergedConfiguration(bool $resolveNull = true): array
    {
        $result = [];
        foreach ($this->configurationList as $configuration) {
            $result = ConfigurationUtility::mergeConfiguration($result, $configuration, false);
        }

        if ($resolveNull) {
            $result = ConfigurationUtility::resolveNullInMergedConfiguration($result);
        }

        return $result;
    }

    public function get(string $key = '', mixed $default = null): mixed
    {
        $configuration = $this->getMergedConfiguration();
        if ($key === '') {
            return $configuration;
        }

        return $configuration[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->readonlyCheck('set');
        if ($this->manualOverride === null) {
            $this->configurationList[] = [];
            $this->manualOverride = &$this->configurationList[count($this->configurationList) - 1];
        }

        $this->manualOverride[$key] = $value;
    }

    public function unset(string $key): void
    {
        $this->readonlyCheck('unset');
        $this->set($key, null);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->getMergedConfiguration());
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set((string)$offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->unset($offset);
    }

    public function getDataProcessingConfiguration(): array
    {
        return $this->get(static::KEY_DATA_PROCESSING, []);
    }

    public function getDataMapperGroupConfiguration(string $id): ?array
    {
        $dataMapperGroups = $this->getDataProcessingConfiguration()[static::KEY_DATA_MAPPER_GROUPS] ?? [];
        if (isset($dataMapperGroups[$id])) {
            return MapUtility::getItemValue($dataMapperGroups[$id]);
        }

        return null;
    }

    public function getConditionConfiguration(string $id): ?array
    {
        $conditions = $this->getDataProcessingConfiguration()[static::KEY_CONDITIONS] ?? [];
        if (isset($conditions[$id])) {
            return MapUtility::getItemValue($conditions[$id]);
        }

        return null;
    }

    public function getValueMapConfiguration(string $id): ?array
    {
        $valueMaps = $this->getDataProcessingConfiguration()[static::KEY_VALUE_MAPS] ?? [];
        if (isset($valueMaps[$id])) {
            return MapUtility::getItemValue($valueMaps[$id]);
        }

        return null;
    }

    public function getGeneralIntegrationConfiguration(): array
    {
        return $this->getIntegrationConfiguration(static::KEY_GENERAL_INTEGRATION);
    }

    public function integrationExists(string $integrationName): bool
    {
        return isset($this->get(static::KEY_INTEGRATIONS, [])[$integrationName]);
    }

    public function getIntegrationConfiguration(string $integrationName): array
    {
        return $this->get(static::KEY_INTEGRATIONS, [])[$integrationName] ?? [];
    }

    public function getAllIntegrationConfigurations(): array
    {
        return $this->get(static::KEY_INTEGRATIONS, []);
    }

    public function getAllIntegrationNames(): array
    {
        $names = array_keys($this->getAllIntegrationConfigurations());

        return array_filter($names, static fn (string $name) => $name !== self::KEY_GENERAL_INTEGRATION);
    }

    public function getIdentifierCollectorConfiguration(string $integrationName, string $identifierCollectorName): array
    {
        return $this->getIntegrationConfiguration($integrationName)[static::KEY_IDENTIFIERS][$identifierCollectorName] ?? [];
    }

    public function identifierCollectorExists(string $integrationName, string $identifierCollectorName): bool
    {
        return isset($this->getIntegrationConfiguration($integrationName)[static::KEY_IDENTIFIERS][$identifierCollectorName]);
    }
}
