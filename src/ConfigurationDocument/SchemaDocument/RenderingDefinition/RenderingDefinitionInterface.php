<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition\AndCondition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition\Condition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value\ScalarValues;

interface RenderingDefinitionInterface
{
    public const FORMAT_SELECT = 'select';

    public const FORMAT_TEXT = 'text';

    public const FORMAT_HIDDEN = 'hidden';

    public const TRIGGER_SWITCH = 'switch';

    public const GROUP_SECONDARY = 'secondary';

    /**
     * @return ?array<string,mixed>
     */
    public function toArray(): ?array;

    public function setFormat(string $format): void;

    public function hideLabel(bool $hide = true): void;

    public function setLabel(?string $label): void;

    public function getLabel(): ?string;

    public function setGroup(string $group): void;

    public function getGroup(): ?string;

    public function getVisibility(): AndCondition;

    public function addVisibilityCondition(Condition $condition): void;

    public function addVisibilityConditionByValue(string $path): ScalarValues;

    public function setNavigationItem(bool $value): void;

    public function getNavigationItem(): bool;

    public function setSkipInNavigation(bool $skipInNavigation): void;

    public function getSkipInNavigation(): bool;

    public function setSkipHeader(bool $skipHeader): void;

    public function getSkipHeader(): bool;

    public function addTrigger(string $triggerName): void;
}
