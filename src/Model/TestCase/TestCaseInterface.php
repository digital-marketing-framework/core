<?php

namespace DigitalMarketingFramework\Core\Model\TestCase;

use DigitalMarketingFramework\Core\Model\ItemInterface;

interface TestCaseInterface extends ItemInterface
{
    public function getLabel(): string;

    public function setLabel(string $label): void;

    public function getName(): string;

    public function setName(string $name): void;

    public function getDescription(): string;

    public function setDescription(string $description): void;

    public function getType(): string;

    public function setType(string $type): void;

    public function getHash(): string;

    public function setHash(string $hash): void;

    /**
     * @return array<string,mixed>
     */
    public function getInput(): array;

    /**
     * @param array<string,mixed> $input
     */
    public function setInput(array $input): void;

    /**
     * @return array<string,mixed>
     */
    public function getExpectedOutput(): array;

    /**
     * @param array<string,mixed> $expectedOutput
     */
    public function setExpectedOutput(array $expectedOutput): void;
}
