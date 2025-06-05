<?php

namespace DigitalMarketingFramework\Core\Model\TestCase;

class TestCase implements TestCaseInterface
{
    public function __construct(
        protected string $label,
        protected string $name,
        protected string $type,
        protected string $hash,
        protected array $input,
        protected array $expectedOutput,
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getInput(): array
    {
        return $this->input;
    }

    public function setInput(array $input): void
    {
        $this->input = $input;
    }

    public function getExpectedOutput(): array
    {
        return $this->expectedOutput;
    }

    public function setExpectedOutput(array $expectedOutput): void
    {
        $this->expectedOutput = $expectedOutput;
    }
}
