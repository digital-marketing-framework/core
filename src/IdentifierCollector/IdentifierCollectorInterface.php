<?php

namespace DigitalMarketingFramework\Core\IdentifierCollector;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface IdentifierCollectorInterface extends PluginInterface
{
    public function addContext(ContextInterface $source, WriteableContextInterface $target): void;

    public function getIdentifier(ContextInterface $context): ?IdentifierInterface;

    public static function getSchema(): SchemaInterface;
}
