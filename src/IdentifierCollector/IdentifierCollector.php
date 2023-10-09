<?php

namespace DigitalMarketingFramework\Core\IdentifierCollector;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePlugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class IdentifierCollector extends ConfigurablePlugin implements IdentifierCollectorInterface
{
    protected const KEY_ENABLED = 'enabled';

    protected const DEFAULT_ENABLED = false;

    public function __construct(
        string $keyword,
        protected RegistryInterface $registry,
        protected ConfigurationInterface $identifiersConfiguration
    ) {
        parent::__construct($keyword);
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

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->addProperty(static::KEY_ENABLED, new BooleanSchema(static::DEFAULT_ENABLED));

        return $schema;
    }
}
