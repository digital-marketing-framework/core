<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition;

use DigitalMarketingFramework\Core\SchemaDocument\Condition\AndCondition;
use DigitalMarketingFramework\Core\SchemaDocument\Condition\Condition;
use DigitalMarketingFramework\Core\SchemaDocument\Condition\InCondition;
use DigitalMarketingFramework\Core\SchemaDocument\Value\ScalarValues;

class RenderingDefinition implements RenderingDefinitionInterface
{
    protected AndCondition $visibility;

    protected ?string $format = null;

    protected ?string $icon = null;

    protected bool $sortAlphabetically = false;

    /**
     * @var array<array{path:string,label?:string,icon?:string}>
     */
    protected array $references = [];

    protected ?bool $hideLabel = null;

    protected ?string $label = null;

    protected ?bool $isNavigationItem = null;

    protected ?bool $skipInNavigation = null;

    protected ?bool $skipHeader = null;

    protected ?string $group = null;

    /** @var array<string> */
    protected array $triggers = [];

    /** @var array<string> */
    protected array $roles = [];

    protected string $description = '';

    protected string $hint = '';

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

        if ($this->icon !== null) {
            $render['icon'] = $this->icon;
        }

        if ($this->sortAlphabetically) {
            $render['sortAlphabetically'] = true;
        }

        if ($this->references !== []) {
            $render['references'] = $this->references;
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

        if ($this->roles !== []) {
            $render['roles'] = $this->roles;
        }

        if ($this->group !== null) {
            $render['group'] = $this->group;
        }

        if ($this->description !== '') {
            $render['description'] = $this->description;
        }

        if ($this->hint !== '') {
            $render['hint'] = $this->hint;
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

    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function sortAlphabetically(bool $sort = true): void
    {
        $this->sortAlphabetically = $sort;
    }

    public function addReference(string $path, ?string $label = null, ?string $icon = null): void
    {
        $reference = [
            'path' => $path,
        ];
        if ($label === null && $icon === null) {
            $reference['label'] = '{.}';
        } else {
            if ($label !== null) {
                $reference['label'] = $label;
            }

            if ($icon !== null) {
                $reference['icon'] = $icon;
            }
        }

        $this->references[] = $reference;
    }

    public function getReferences(): array
    {
        return $this->references;
    }

    public function setReferences(array $references): void
    {
        $this->references = $references;
    }

    public function hideLabel(bool $hide = true): void
    {
        $this->hideLabel = $hide;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function setGeneralDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getGeneralDescription(): string
    {
        return $this->getGeneralDescription();
    }

    public function setHint(string $hint): void
    {
        $this->hint = $hint;
    }

    public function getHint(): string
    {
        return $this->hint;
    }
}
