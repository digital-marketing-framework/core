<?php

namespace DigitalMarketingFramework\Core\IdentifierCollector;

use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\Integration\IntegrationPluginInterface;
use DigitalMarketingFramework\Core\Model\Identifier\IdentifierInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

interface IdentifierCollectorInterface extends IntegrationPluginInterface
{
    public function addContext(WriteableContextInterface $context): void;

    public function getIdentifier(): ?IdentifierInterface;

    public static function getSchema(): SchemaInterface;
}
