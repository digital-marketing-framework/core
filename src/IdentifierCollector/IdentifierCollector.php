<?php

namespace DigitalMarketingFramework\Core\IdentifierCollector;

use DigitalMarketingFramework\Core\Context\ContextAwareInterface;
use DigitalMarketingFramework\Core\Context\ContextAwareTrait;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareInterface;
use DigitalMarketingFramework\Core\DataPrivacy\DataPrivacyManagerAwareTrait;
use DigitalMarketingFramework\Core\Integration\IntegrationInfo;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Plugin\IntegrationPlugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\Icon;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\DataPrivacyPermissionSelectionSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

abstract class IdentifierCollector extends IntegrationPlugin implements IdentifierCollectorInterface, ContextAwareInterface, DataPrivacyManagerAwareInterface
{
    use ContextAwareTrait;
    use DataPrivacyManagerAwareTrait;

    protected const KEY_ENABLED = 'enabled';

    protected const DEFAULT_ENABLED = false;

    protected const KEY_REQUIRED_PERMISSION = 'requiredPermission';

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
        if (!$this->getConfig(static::KEY_ENABLED)) {
            return false;
        }

        $permission = $this->getConfig(static::KEY_REQUIRED_PERMISSION);

        return $this->dataPrivacyManager->getPermission($permission);
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
        $schema->getRenderingDefinition()->setIcon(Icon::IDENTIFIER_COLLECTOR);

        $label = static::getLabel();
        if ($label !== null) {
            $schema->getRenderingDefinition()->setLabel($label);
        }

        $schema->addProperty(static::KEY_ENABLED, new BooleanSchema(static::DEFAULT_ENABLED));

        $schema->addProperty(static::KEY_REQUIRED_PERMISSION, new CustomSchema(DataPrivacyPermissionSelectionSchema::TYPE));

        return $schema;
    }
}
