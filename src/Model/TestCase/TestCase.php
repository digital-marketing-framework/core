<?php

namespace DigitalMarketingFramework\Core\Model\TestCase;

use DateTime;
use DigitalMarketingFramework\Core\Model\Item;
use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;
use JsonException;

class TestCase extends Item implements TestCaseInterface
{
    public function __construct(
        protected string $label = '',
        protected string $name = '',
        protected string $description = '',
        protected string $type = '',
        protected string $hash = '',
        protected string $serializedInput = '',
        protected string $serializedExpectedOutput = '',
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
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

    public function getSerializedInput(): string
    {
        return $this->serializedInput;
    }

    public function setSerializedInput(string $serializedInput): void
    {
        $this->serializedInput = $serializedInput;
    }

    public function getInput(): array
    {
        $data = $this->getSerializedInput();
        if ($data === '') {
            return [];
        }

        try {
            return json_decode($data, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
    }

    public function setInput(array $input): void
    {
        try {
            $serializedData = json_encode($input, flags: JSON_THROW_ON_ERROR);
            $this->setSerializedInput($serializedData);
        } catch (JsonException) {
            try {
                $serializedData = json_encode($input, flags: JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
                $this->setSerializedInput($serializedData);
            } catch (JsonException) {}
        }
    }

    public function getSerializedExpectedOutput(): string
    {
        return $this->serializedExpectedOutput;
    }

    public function setSerializedExpectedOutput(string $serializedExpectedOutput): void
    {
        $this->serializedExpectedOutput = $serializedExpectedOutput;
    }

    public function getExpectedOutput(): array
    {
        $data = $this->getSerializedExpectedOutput();
        if ($data === '') {
            return [];
        }

        try {
            return json_decode($data, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
    }

    public function setExpectedOutput(array $expectedOutput): void
    {
        try {
            $serializedData = json_encode($expectedOutput, flags: JSON_THROW_ON_ERROR);
            $this->setSerializedExpectedOutput($serializedData);
        } catch (JsonException) {
            try {
                $serializedData = json_encode($expectedOutput, flags: JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
                $this->setSerializedExpectedOutput($serializedData);
            } catch (JsonException) {}
        }
    }
}
