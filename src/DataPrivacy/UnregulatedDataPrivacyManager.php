<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

use DigitalMarketingFramework\Core\Context\WriteableContextInterface;

class UnregulatedDataPrivacyManager extends DataPrivacyManager
{
    public function hasPermission(string $level): bool
    {
        return true;
    }

    public function getPermissionLevels(): array
    {
        return ['all'];
    }

    public function addContext(WriteableContextInterface $context): void
    {
    }
}
