<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;
use DigitalMarketingFramework\Core\Storage\ItemStorageInterface;

/**
 * @extends ItemStorageInterface<TestCaseInterface>
 */
interface TestCaseStorageInterface extends ItemStorageInterface
{
    /**
     * @return array<TestCaseInterface>
     */
    public function fetchByType(string $type): array;

    /**
     * @return array<TestCaseInterface>
     */
    public function fetchByName(string $name): array;

    /**
     * @return array<string>
     */
    public function fetchAllTypes(): array;
}
