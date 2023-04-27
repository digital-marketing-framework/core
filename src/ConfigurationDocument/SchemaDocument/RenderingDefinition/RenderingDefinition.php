<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\RenderingDefinition;

class RenderingDefinition implements RenderingDefinitionInterface
{
    protected ?array $visibility = null;
    protected array $alignments = [];
    protected ?string $format = null;
    protected ?bool $hideLabel = null;

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
        if (!empty($this->alignments)) {
            $render['alignments'] = $this->alignments;
        }
        return !empty($render) ? $render : null;
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

    public function addAlignment(string $alignment, array $fields): void
    {
        $this->alignments[] = [
            'alignment' => $alignment,
            'items' => $fields,
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
