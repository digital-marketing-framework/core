<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition\AndCondition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition\Condition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition\InCondition;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value\ScalarValues;

class RenderingDefinition implements RenderingDefinitionInterface
{
    protected AndCondition $visibility;

    protected ?string $format = null;

    protected ?bool $hideLabel = null;

    protected ?string $label = null;

    protected ?bool $isNavigationItem = null;

    protected ?bool $skipInNavigation = null;

    protected ?bool $skipHeader = null;

    protected ?string $group = null;

    /** @var array<string> */
    protected array $triggers = [];

    public function __construct()
    {
        $this->visibility = new AndCondition();
    }

    public function toArray(): ?array
    {
        $render = [];
        if ($this->format !== null) {
            $render['format'] = $this->format;
        }

        if ($this->hideLabel ?? false) {
            $render['hideLabel'] = true;
        }

        if ($this->visibility->getConditionCount() > 0) {
            $render['visibility'] = $this->visibility->toArray();
        }

        if ($this->isNavigationItem !== null) {
            $render['navigationItem'] = $this->isNavigationItem;
        }

        if ($this->skipInNavigation !== null) {
            $render['skipInNavigation'] = $this->skipInNavigation;
        }

        if ($this->label !== null) {
            $render['label'] = $this->label;
        }

        if ($this->skipHeader !== null) {
            $render['skipHeader'] = $this->skipHeader;
        }

        if ($this->triggers !== []) {
            $render['triggers'] = $this->triggers;
        }

        if ($this->group !== null) {
            $render['group'] = $this->group;
        }

        return empty($render) ? null : $render;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setGroup(string $group): void
    {
        $this->group = $group;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function addTrigger(string $triggerName): void
    {
        if (!in_array($triggerName, $this->triggers)) {
            $this->triggers[] = $triggerName;
        }
    }

    public function setSkipHeader(bool $skipHeader): void
    {
        $this->skipHeader = $skipHeader;
    }

    public function getSkipHeader(): bool
    {
        return $this->skipHeader;
    }

    public function setSkipInNavigation(bool $skipInNavigation): void
    {
        $this->skipInNavigation = $skipInNavigation;
    }

    public function getSkipInNavigation(): bool
    {
        return $this->skipInNavigation;
    }

    public function setNavigationItem(bool $value): void
    {
        $this->isNavigationItem = $value;
    }

    public function getNavigationItem(): bool
    {
        return $this->isNavigationItem;
    }

    public function getVisibility(): AndCondition
    {
        return $this->visibility;
    }

    public function addVisibilityCondition(Condition $condition): void
    {
        $this->visibility->addCondition($condition);
    }

    public function addVisibilityConditionByValue(string $path): ScalarValues
    {
        $values = new ScalarValues();
        $this->addVisibilityCondition(new InCondition($path, $values));

        return $values;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function hideLabel(bool $hide = true): void
    {
        $this->hideLabel = $hide;
    }
}
