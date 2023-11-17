<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

use DigitalMarketingFramework\Core\Context\WriteableContextInterface;

interface DataPrivacyManagerInterface
{
    public function hasPermission(string $level): bool;

    /**
     * @return array<string>
     */
    public function getPermissionLevels(): array;

    public function addContext(WriteableContextInterface $context): void;
}
