<?php

namespace DigitalMarketingFramework\Core\DataPrivacy;

use DigitalMarketingFramework\Core\Context\ContextAwareInterface;
use DigitalMarketingFramework\Core\Context\ContextAwareTrait;
use DigitalMarketingFramework\Core\Context\WriteableContextInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class DataPrivacyPlugin implements DataPrivacyPluginInterface, ContextAwareInterface, GlobalConfigurationAwareInterface
{
    use ContextAwareTrait;
    use GlobalConfigurationAwareTrait;

    public function __construct(
        protected string $keyword = '',
    ) {
    }

    abstract public function addContext(WriteableContextInterface $context): void;

    abstract public function getAllPossiblePermissions(): array;

    public function getLabel(): string
    {
        return GeneralUtility::getLabelFromValue($this->getKeyword());
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function permissionMatches(string $permission): bool
    {
        return in_array($permission, $this->getAllPossiblePermissions());
    }

    abstract public function getGrantedPermissions(): array;

    abstract public function getPermissionLabels(): array;

    abstract public function getPermissionDefaults(): array;

    public function getPermission(string $permission): bool
    {
        return in_array($permission, $this->getGrantedPermissions());
    }

    public function getPermissionDefault(string $permission): bool
    {
        return $this->getPermissionDefaults()[$permission] ?? false;
    }

    public function getPermissionLabel(string $permission): string
    {
        return $this->getPermissionLabels()[$permission] ?? '';
    }

    public function getFrontendSettings(): array
    {
        return [];
    }
}
