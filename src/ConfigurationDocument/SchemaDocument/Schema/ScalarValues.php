<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

class ScalarValues
{
    /**
     * @param array<string|int|bool> $list
     * @param array<string> $sets
     */
    public function __construct(
        protected array $list = [],
        protected array $sets = [],
    ) {
    }

    public function addValue(string|int|bool $value): void
    {
        $this->list[] = $value;
    }

    public function addValueSet(string $name): void
    {
        $this->sets[] = $name;
    }

    public function reset(): void
    {
        $this->list = [];
        $this->sets = [];
    }

    public function toArray(): ?array
    {
        if (empty($this->list) && empty($this->sets)) {
            return null;
        }
        return [
            'list' => !empty($this->list) ? $this->list : null,
            'sets' => !empty($this->sets) ? $this->sets : null,
        ];
    }
}
