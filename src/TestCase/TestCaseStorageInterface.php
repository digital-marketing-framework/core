<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;
use DigitalMarketingFramework\Core\Storage\ItemStorageInterface;

/**
 * @template TestCaseClass of TestCaseInterface
 * @template IdType of int|string
 *
 * @extends ItemStorageInterface<TestCaseClass,IdType>
 */
interface TestCaseStorageInterface extends ItemStorageInterface
{
    /**
     * @return array<TestCaseClass>
     */
    public function fetchByType(string $type): array;

    /**
     * @return array<TestCaseClass>
     */
    public function fetchByName(string $name): array;

    /**
     * @return array<string>
     */
    public function fetchAllTypes(): array;
}
