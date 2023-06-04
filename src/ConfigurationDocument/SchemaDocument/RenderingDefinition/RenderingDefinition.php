<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition;

class RenderingDefinition implements RenderingDefinitionInterface
{
    protected ?array $visibility = null;
    protected ?string $format = null;
    protected ?bool $hideLabel = null;
    protected ?string $label = null;
    protected ?bool $isNavigationItem = null;
    protected ?bool $skipHeader = null;
    protected array $triggers = [];

    public function toArray(): ?array
    {
        $render = [];
        if ($this->format !== null) {
            $render['format'] = $this->format;
        }
        if ($this->hideLabel ?? false) {
            $render['hideLabel'] = true;
        }
        if ($this->visibility !== null) {
            $render['visibility'] = $this->visibility;
        }
        if ($this->isNavigationItem !== null) {
            $render['navigationItem'] = $this->isNavigationItem;
        }
        if ($this->label !== null) {
            $render['label'] = $this->label;
        }
        if ($this->skipHeader !== null) {
            $render['skipHeader'] = $this->skipHeader;
        }
        if (!empty($this->triggers)) {
            $render['triggers'] = $this->triggers;
        }
        return !empty($render) ? $render : null;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getLabel(): ?string
    {
        return $this->label;
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

    public function setNavigationItem(bool $value): void
    {
        $this->isNavigationItem = $value;
    }

    public function getNavigationItem(): bool
    {
        return $this->isNavigationItem;
    }

    public function setVisibilityConditionByString(string $path, string $value): void
    {
        $this->visibility = [
            'type' => 'string',
            'path' => $path,
            'value' => $value,
        ];
    }

    public function setVisibilityConditionByValueSet(string $path, string $set): void
    {
        $this->visibility = [
            'type' => 'valueSet',
            'path' => $path,
            'set' => $set,
        ];
    }

    public function setVisibilityConditionByBoolean(string $path): void
    {
        $this->visibility = [
            'type' => 'bool',
            'path' => $path,
        ];
    }

    public function setVisibilityConditionByToggle(string $path): void
    {
        $this->visibility = [
            'type' => 'toggle',
            'path' => $path,
        ];
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
