<?php

namespace DigitalMarketingFramework\Core\Model\TestCase;

use DigitalMarketingFramework\Core\Model\ItemInterface;

/**
 * @template IdType of int|string
 *
 * @extends ItemInterface<IdType>
 */
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

    public function getInput(): array;
    public function setInput(array $input): void;

    public function getExpectedOutput(): array;
    public function setExpectedOutput(array $expectedOutput): void;
}
