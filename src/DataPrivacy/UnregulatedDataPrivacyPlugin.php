<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

use DigitalMarketingFramework\Core\Context\WriteableContextInterface;

class UnregulatedDataPrivacyPlugin extends DataPrivacyPlugin
{
    public const PERMISSION_ALLOWED = 'allowed';

    public const PERMISSION_ALLOWED_LABEL = 'Always allowed';

    public const PERMISSION_DENIED = 'denied';

    public const PERMISSION_DENIED_LABEL = 'Never allowed';

    public function __construct()
    {
        parent::__construct('unregulated');
    }

    public function addContext(WriteableContextInterface $context): void
    {
    }

    public function getAllPossiblePermissions(): array
    {
        return [self::PERMISSION_ALLOWED, self::PERMISSION_DENIED];
    }

    public function getGrantedPermissions(): array
    {
        return [self::PERMISSION_ALLOWED];
    }

    public function getPermissionLabels(): array
    {
        return [self::PERMISSION_ALLOWED => self::PERMISSION_ALLOWED_LABEL, self::PERMISSION_DENIED => self::PERMISSION_DENIED_LABEL];
    }

    public function getPermissionDefaults(): array
    {
        return [self::PERMISSION_ALLOWED => true, self::PERMISSION_DENIED => false];
    }
}
