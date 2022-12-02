<?php

namespace DigitalMarketingFramework\Core\Service;

use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContext;
use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Helper\ConfigurationTrait;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class DataProcessor implements DataProcessorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use ConfigurationTrait;

    protected ConfigurationResolverContextInterface $resolverContext;

    public function __construct(
        protected ConfigurationResolverRegistryInterface $registry,
        protected array|string $dataMap,
    ) {
    }

    protected function resolveDataMap(array|string|null $dataMap): ?array
    {
        $references = [];
        /** @var ?ConfigurationInterface */
        $collectorConfiguration = $this->resolverContext['configuration'] ?? null;
        while (!is_array($dataMap) && $dataMap !== null) {
            $dataMap = (string)$dataMap;
            if (in_array($dataMap, $references)) {
                throw new DigitalMarketingFrameworkException(sprintf('Data map reference loop detected for map "%s".', $dataMap));
            }
            $dataMap = $collectorConfiguration?->getDataMapConfiguration($dataMap);
        }
        return $dataMap;
    }

    protected function addField(DataInterface $output, string|int $key, string|ValueInterface $value): void
    {
        if ($value instanceof DataInterface) {
            foreach ($value as $dataKey => $dataValue) {
                $this->addField($output, $dataKey, $dataValue);
            }
        } elseif ($value !== null) {
            if ($output->fieldEmpty($key)) {
                $output[$key] = $value;
            }
        }
    }

    protected function processExplicitFields(DataInterface $output): void
    {
        foreach ($this->getConfig(static::KEY_FIELDS) as $field => $fieldConfig) {
            /** @var GeneralContentResolver $contentResolver */
            $contentResolver = $this->registry->getContentResolver('general', $fieldConfig, $this->resolverContext->copy());
            $value = $contentResolver->resolve();
            if ($value !== null) {
                $this->addField($output, $field, $value);
            }
        }
    }

    protected function processPassthroughFields(DataInterface $output): void
    {
        $passthroughFields = $this->getConfig(static::KEY_PASSTHROUGH_FIELDS);
        if ($passthroughFields) {
            /** @var GeneralContentResolver */
            $contentResolver = $this->registry->getContentResolver('general', ['passthroughFields' => $passthroughFields], $this->resolverContext->copy());
            $passthroughData = $contentResolver->resolve();
            if ($passthroughData instanceof DataInterface) {
                foreach ($passthroughData as $passthroughKey => $passthroughValue) {
                    $this->addField($output, $passthroughKey, $passthroughValue);
                }
            } else {
                $this->logger->error('Content resolver "passthroughFields" did not return a data object - skipping.');
            }
        }
    }

    protected function processFilters(DataInterface $output): void
    {
        $excludedFields = GeneralUtility::castValueToArray($this->getConfig(static::KEY_EXCLUDE_FIELDS));
        $ignoreEmptyFields = $this->getConfig(static::KEY_IGNORE_EMPTY_FIELDS);
        if ($ignoreEmptyFields || !empty($excludedFields)) {
            foreach ($output as $field => $value) {
                if ($ignoreEmptyFields && GeneralUtility::isEmpty($value)) {
                    unset($output[$field]);
                } elseif (in_array($field, $excludedFields)) {
                    unset($output[$field]);
                }
            }
        }
    }

    public function process(DataInterface $data, array $context = []): DataInterface
    {
        $output = new Data();
        
        $this->resolverContext = new ConfigurationResolverContext($data, $context);
        $this->configuration = $this->resolveDataMap($this->dataMap);

        // if no data map configuration is given, the result is empty
        if ($this->configuration === null) {
            return $output;
        }

        // build explicitly defined fields
        $this->processExplicitFields($output);

        // passthrough other fields if configured thusly
        $this->processPassthroughFields($output);

        // filter generated fields as configured
        $this->processFilters($output);
        
        return $output;
    }

    public static function getDefaultConfiguration(): array
    {
        return [
            static::KEY_FIELDS => static::DEFAULT_FIELDS,
            static::KEY_IGNORE_EMPTY_FIELDS => static::DEFAULT_IGNORE_EMPTY_FIELDS,
            static::KEY_EXCLUDE_FIELDS => static::DEFAULT_EXCLUDE_FIELDS,
            static::KEY_PASSTHROUGH_FIELDS => static::DEFAULT_PASSTHROUGH_FIELDS,
        ];
    }
}
