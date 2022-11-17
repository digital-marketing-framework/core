<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\Context\ConfigurationResolverContextInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Registry\Plugin\ConfigurationResolverRegistryInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class FieldCollectorContentResolver extends AbstractFieldCollectorContentResolver
{
    protected const KEY_TEMPLATE = 'template';
    protected const DEFAULT_TEMPLATE = '{key}\s=\s{value}\n';

    protected string $template;

    public function __construct(string $keyword, ConfigurationResolverRegistryInterface $registry, $config, ConfigurationResolverContextInterface $context)
    {
        parent::__construct($keyword, $registry, $config, $context);
        $this->template = GeneralUtility::parseSeparatorString($this->resolveContent($this->getConfig(static::KEY_TEMPLATE)));
    }

    protected function getMultiValue(): MultiValueInterface
    {
        $value = parent::getMultiValue();
        $value->setGlue('');
        return $value;
    }

    protected function processField(string|int $key, string|ValueInterface|null $value): string|ValueInterface|null
    {
        if ($value === null) {
            return null;
        }
        switch ($this->template) {
            case '{key}':
                return $key;
            case '{value}':
                return $value;
            default:
                return str_replace(['{key}', '{value}'], [$key, $value], $this->template);
        }
    }

    protected function addValue(MultiValueInterface $output, string|int $key, string|ValueInterface|null $value): void
    {
        $output[] = $value;
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_TEMPLATE => static::DEFAULT_TEMPLATE,
        ];
    }
}
