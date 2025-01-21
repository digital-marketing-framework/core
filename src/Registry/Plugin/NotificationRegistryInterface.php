<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Notification\NotificationChannelInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManager;
use DigitalMarketingFramework\Core\Notification\NotificationManagerInterface;

interface NotificationRegistryInterface
{
    public function setNotificationManager(NotificationManagerInterface $notificationManager): void;

    public function getNotificationManager(): NotificationManagerInterface;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerNotificationChannel(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteNotificationChannel(string $keyword): void;

    public function getNotificationChannel(string $keyword): ?NotificationChannelInterface;

    /**
     * @return array<NotificationChannelInterface>
     */
    public function getAllNotificationChannels(): array;
}
