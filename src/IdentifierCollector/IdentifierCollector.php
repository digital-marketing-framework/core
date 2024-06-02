<?php

namespace DigitalMarketingFramework\Core\IdentifierCollector;

use DigitalMarketingFramework\Core\Context\ContextAwareInterface;
use DigitalMarketingFramework\Core\Context\ContextAwareTrait;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Integration\IntegrationInfo;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Plugin\IntegrationPlugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class IdentifierCollector extends IntegrationPlugin implements IdentifierCollectorInterface, ContextAwareInterface
{
    use ContextAwareTrait;

    protected const KEY_ENABLED = 'enabled';

    protected const DEFAULT_ENABLED = false;

    public function __construct(
        string $keyword,
        protected RegistryInterface $registry,
        protected ConfigurationInterface $identifierConfiguration,
        ?IntegrationInfo $integrationInfo = null
    ) {
        parent::__construct(
            $keyword,
            $integrationInfo ?? static::getDefaultIntegrationInfo(),
            $identifierConfiguration
        );
        $this->configuration = $identifierConfiguration->getIdentifierCollectorConfiguration(
            $this->integrationInfo->getName(),
            $this->getKeyword()
        );
    }

    abstract public static function getDefaultIntegrationInfo(): IntegrationInfo;

    public function getIntegrationInfo(): IntegrationInfo
    {
        return $this->integrationInfo;
    }

    protected function proceed(): bool
    {
        return (bool)$this->getConfig(static::KEY_ENABLED);
    }

    abstract protected function prepareContext(WriteableContextInterface $context): void;

    public function addContext(WriteableContextInterface $context): void
    {
        if ($this->proceed()) {
            $this->prepareContext($context);
        }
    }

    abstract protected function collect(): ?IdentifierInterface;

    public function getIdentifier(): ?IdentifierInterface
    {
        if ($this->proceed()) {
            return $this->collect();
        }

        return null;
    }

    public static function getSchema(): SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setIcon('identifier-collector');

        $label = static::getLabel();
        if ($label !== null) {
            $schema->getRenderingDefinition()->setLabel($label);
        }

        $schema->addProperty(static::KEY_ENABLED, new BooleanSchema(static::DEFAULT_ENABLED));

        return $schema;
    }
}
