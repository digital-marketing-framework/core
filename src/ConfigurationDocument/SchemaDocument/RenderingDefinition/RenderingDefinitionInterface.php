<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition;

interface RenderingDefinitionInterface
{
    public function toArray(): ?array;

    public function setFormat(string $format): void;
    public function hideLabel(bool $hide = true): void;
    public function setLabel(?string $label): void;
    public function getLabel(): ?string;

    public function setVisibilityConditionByString(string $path, string $value): void;
    public function setVisibilityConditionByValueSet(string $path, string $set): void;
    public function setVisibilityConditionByBoolean(string $path): void;
    public function setVisibilityConditionByToggle(string $path): void;

    public function setNavigationItem(bool $value): void;
    public function getNavigationItem(): bool;

    public function addAlignment(string $alignment, array $fields): void;
}
