<?php

namespace DigitalMarketingFramework\Core\IdentifierCollector;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Helper\ConfigurationTrait;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\Plugin\IdentifierCollectorRegistryInterface;

abstract class IdentifierCollector extends Plugin implements IdentifierCollectorInterface
{
    use ConfigurationTrait;

    protected const KEY_ENABLED = 'enabled';
    protected const DEFAULT_ENABLED = false;

    public function __construct(
        string $keyword,
        IdentifierCollectorRegistryInterface $registry,
        protected ConfigurationInterface $identifiersConfiguration
    ) {
        parent::__construct($keyword, $registry);
        $this->configuration = $identifiersConfiguration->getIdentifierCollectorConfiguration($this->getKeyword());
    }

    protected function proceed(): bool
    {
        return (bool)$this->getConfig(static::KEY_ENABLED);
    }

    abstract protected function prepareContext(ContextInterface $source, WriteableContextInterface $target): void;

    public function addContext(ContextInterface $source, WriteableContextInterface $target): void
    {
        if ($this->proceed()) {
            $this->prepareContext($source, $target);
        }
    }

    abstract protected function collect(ContextInterface $context): ?IdentifierInterface;

    public function getIdentifier(ContextInterface $context): ?IdentifierInterface
    {
        if ($this->proceed()) {
            return $this->collect($context);
        }
        return null;
    }

    public static function getDefaultConfiguration(): array
    {
        return [
            static::KEY_ENABLED => static::DEFAULT_ENABLED,
        ];
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->addProperty(static::KEY_ENABLED, new BooleanSchema(static::DEFAULT_ENABLED));
        return $schema;
    }
}
