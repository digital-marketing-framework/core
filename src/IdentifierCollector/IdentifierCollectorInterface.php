<?php

namespace DigitalMarketingFramework\Core\IdentifierCollector;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePluginInterface;

interface IdentifierCollectorInterface extends ConfigurablePluginInterface
{
    public function addContext(ContextInterface $source, WriteableContextInterface $target): void;

    public function getIdentifier(ContextInterface $context): ?IdentifierInterface;

    public static function getSchema(): SchemaInterface;
}
