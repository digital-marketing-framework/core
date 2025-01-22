<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Notification\NotificationChannelInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManagerInterface;

trait NotificationRegistryTrait
{
    use PluginRegistryTrait;

    protected NotificationManagerInterface $notificationManager;

    abstract public function createObject(string $class, array $arguments = []): object;

    public function getNotificationManager(): NotificationManagerInterface
    {
        return $this->getRegistryCollection()->getNotificationManager();
    }

    public function setNotificationManager(NotificationManagerInterface $notificationManager): void
    {
        $this->getRegistryCollection()->setNotificationManager($notificationManager);
    }

    public function registerNotificationChannel(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(NotificationChannelInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteNotificationChannel(string $keyword): void
    {
        $this->deletePlugin($keyword, NotificationChannelInterface::class);
    }

    public function getNotificationChannel(string $keyword): ?NotificationChannelInterface
    {
        return $this->getPlugin($keyword, NotificationChannelInterface::class);
    }

    public function getAllNotificationChannels(): array
    {
        return $this->getAllPlugins(NotificationChannelInterface::class);
    }
}
