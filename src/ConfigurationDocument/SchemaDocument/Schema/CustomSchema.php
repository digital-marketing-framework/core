<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

class CustomSchema extends Schema
{
    public function __construct(
        protected string $type,
    ) {
        parent::__construct();
    }

    protected function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    protected function getConfig(): ?array
    {
        return null;
    }
}
