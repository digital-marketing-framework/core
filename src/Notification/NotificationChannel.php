<?php

namespace DigitalMarketingFramework\Core\Notification;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class NotificationChannel implements NotificationChannelInterface, GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    protected array $configuration;

    abstract public function notify(string $message, string $component, int $level): void;

    abstract protected function getConfigPackageKey(): string;

    protected function getConfiguration(): array
    {
        if (!isset($this->configuration)) {
            $this->configuration = $this->globalConfiguration->get($this->getConfigPackageKey());
        }

        return $this->configuration;
    }

    protected function getAllowListFromString(string $list): array
    {
        return GeneralUtility::castValueToArray($list);
    }

    protected function checkAllowList(string $list, string $item): bool
    {
        $allowList = GeneralUtility::castValueToArray($list);
        foreach ($allowList as $allowed) {
            $regexpAllowed = str_replace('*', '.*');
            if (preg_match('/^' . $allowed . '/', $item)) {
                return true;
            }
        }

        return false;
    }

    public function enabled(): bool
    {
        $config = $this->getConfiguration();

        return $config[GlobalNotificationChannelConfigurationSchema::KEY_ENABLED] ?? GlobalNotificationChannelConfigurationSchema::DEFAULT_ENABLED;
    }

    public function acceptComponent(string $component): bool
    {
        $config = $this->getConfiguration();
        $list = $config[GlobalNotificationChannelConfigurationSchema::KEY_COMPONENTS] ?? GlobalNotificationChannelConfigurationSchema::DEFAULT_COMPONENTS;

        return $this->checkAllowLiist($list, $component);
    }

    public function acceptLevel(int $level): bool
    {
        $config = $this->getConfiguration();
        $list = $config[GlobalNotificationChannelConfigurationSchema::KEY_LEVELS] ?? GlobalNotificationChannelConfigurationSchema::DEFAULT_LEVELS;

        return $this->checkAllowLiist($list, (string) $level);
    }

    public function accept(string $component, int $level): bool
    {
        return $this->enabled() && $this->acceptComponent($component) && $this->acceptLevel($level);
    }
}
