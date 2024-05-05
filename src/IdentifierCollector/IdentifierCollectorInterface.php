<?php

namespace DigitalMarketingFramework\Core\IdentifierCollector;

use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Integration\IntegrationPluginInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

interface IdentifierCollectorInterface extends IntegrationPluginInterface
{
    public function addContext(ContextInterface $source, WriteableContextInterface $target): void;

    public function getIdentifier(ContextInterface $context): ?IdentifierInterface;

    public static function getSchema(): SchemaInterface;
}
