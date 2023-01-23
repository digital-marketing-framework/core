<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class MapContentResolver extends ContentResolver
{
    protected const WEIGHT = 100;

    protected const KEY_REFERENCES = 'references';
    protected const DEFAULT_REFERENCES = [];

    protected const KEY_IGNORE_CASE = 'ignoreCase';
    protected const DEFAULT_IGNORE_CASE = false;

    protected const KEY_INVERT = 'invert';
    protected const DEFAULT_INVERT = false;

    protected const KEY_VALUES = 'values';
    protected const DEFAULT_VALUES = [];

    protected function addMap(array &$map, array $additionalMap): void
    {
        foreach ($additionalMap as $value => $mappedValue) {
            if ($mappedValue === null) {
                unset($map[$value]);
            } else {
                $map[$value] = $mappedValue;
            }
        }
    }

    protected function buildMap(): array
    {
        $map = [];

        // references
        $references = $this->getConfig(static::KEY_REFERENCES);
        if (!empty($references)) {
            $configuration = $this->context['configuration'] ?? [];
            $dataMaps = $configuration[ConfigurationInterface::KEY_VALUE_MAPS] ?? [];
            foreach ($references as $reference) {
                $referencedMap = $dataMaps[$reference] ?? [];
                $this->addMap($map, $referencedMap);
            }
        }
        
        // direct values
        $values = $this->getConfig(static::KEY_VALUES);
        $this->addMap($map, $values);

        // invert
        if ($this->getConfig(static::KEY_INVERT)) {
            $map = array_flip($map);
        }

        // ignoreCase
        if ($this->getConfig(static::KEY_IGNORE_CASE)) {
            $ignoreCaseMap = [];
            foreach ($map as $value => $mappedValue) {
                $ignoreCaseMap[strtolower($value)] = $mappedValue;
            }
            $map = $ignoreCaseMap;
        }

        return $map;
    }

    protected function map(string|ValueInterface $value, array $map): string|ValueInterface
    {
        if ($value instanceof MultiValueInterface) {
            $mapped = GeneralUtility::copyMultiValue($value, copyValues:false);
            foreach ($value as $subKey => $subValue) {
                $mapped[$subKey] = $this->map($subValue, $map);
            }
        } else {
            $compareValue = $this->getConfig(static::KEY_IGNORE_CASE) ? strtolower((string)$value) : (string)$value;
            $mapped = $map[$compareValue] ?? $value;
        }
        return $mapped;
    }

    protected function updateConfiguration(): void
    {
        if (!is_array($this->configuration)) {
            // map: "mapName"
            // >
            // map:
            //   references:
            //     - "mapName"
            $this->configuration = [static::KEY_REFERENCES => [$this->configuration]];
        } else {
            // map:
            //   value_a: mapped_value_a
            //   value_b: mapped_value_b
            // >
            // map:
            //   values:
            //     value_a: mapped_value_a
            //     value_b: mapped_value_b
            $defaultConfiguration = static::getDefaultConfiguration();
            $keyword = '';
            $nonKeywordFound = false;
            foreach (array_keys($this->configuration) as $key) {
                if (array_key_exists($key, $defaultConfiguration)) {
                    $keyword = $key;
                } else {
                    $nonKeywordFound = true;
                }
            }
            if ($keyword === '' && $nonKeywordFound) {
                $this->configuration = [static::KEY_VALUES => $this->configuration];
            } elseif ($nonKeywordFound) {
                throw new DigitalMarketingFrameworkException(sprintf('Found keyword "%s" in what is probably meant to be a value map.', $keyword));
            }
        }
    }

    public function finish(string|ValueInterface|null &$result): bool
    {
        $this->updateConfiguration();

        if ($result !== null) {
            $map = $this->buildMap();
            $result = $this->map($result, $map);
        }
        
        return false;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_REFERENCES => static::DEFAULT_REFERENCES,
            static::KEY_IGNORE_CASE => static::DEFAULT_IGNORE_CASE,
            static::KEY_INVERT => static::DEFAULT_INVERT,
            static::KEY_VALUES => static::DEFAULT_VALUES,
        ];
    }
}
