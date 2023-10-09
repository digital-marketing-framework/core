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

    public function getValueMapConfiguration(string $id): ?array
    {
        $valueMaps = $this->get(static::KEY_VALUE_MAPS, []);
        if (isset($valueMaps[$id])) {
            return MapUtility::getItemValue($valueMaps[$id]);
        }

        return null;
    }

    /**
     * @return array<string,mixed>
     */
    protected function getIdentifierConfiguration(bool $resolveNull = true): array
    {
        return $this->getMergedConfiguration($resolveNull)[static::KEY_IDENTIFIER] ?? [];
    }

    public function getIdentifierCollectorConfiguration(string $identifierCollectorName): array
    {
        return $this->getIdentifierConfiguration()[static::KEY_IDENTIFIER_COLLECTORS][$identifierCollectorName] ?? [];
    }

    public function identifierCollectorExists(string $identifierCollectorName): bool
    {
        return isset($this->getIdentifierConfiguration()[static::KEY_IDENTIFIER_COLLECTORS][$identifierCollectorName]);
    }
}
