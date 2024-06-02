<?php

namespace DigitalMarketingFramework\Core\Integration;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class IntegrationInfo
{
    public const WEIGHT_DEFAULT = 50;

    public const WEIGHT_TOP = 10;

    public const WEIGHT_BOTTOM = 100;

    protected string $inboundRouteListLabel;

    protected string $outboundRouteListLabel;

    public function __construct(
        protected string $name,
        protected ?string $label = null,
        protected ?string $icon = 'integration',
        protected int $weight = self::WEIGHT_DEFAULT,
        ?string $inboundRouteListLabel = null,
        ?string $outboundRouteListLabel = null,
    ) {
        $label = $this->getLabel() ?? GeneralUtility::getLabelFromValue($this->getName());
        $this->inboundRouteListLabel = $inboundRouteListLabel ?? 'Routes from ' . $label;
        $this->outboundRouteListLabel = $outboundRouteListLabel ?? 'Routes to ' . $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function getInboundRouteListLabel(): string
    {
        return $this->inboundRouteListLabel;
    }

    public function setInboundRouteListLabel(string $inboundRouteListLabel): void
    {
        $this->inboundRouteListLabel = $inboundRouteListLabel;
    }

    public function getOutboundRouteListLabel(): string
    {
        return $this->outboundRouteListLabel;
    }

    public function setOutboundRouteListLabel(string $outboundRouteListLabel): void
    {
        $this->outboundRouteListLabel = $outboundRouteListLabel;
    }

    public function addSchema(SchemaDocument $schemaDocument, ContainerSchema $integrationSchema): void
    {
    }
}
