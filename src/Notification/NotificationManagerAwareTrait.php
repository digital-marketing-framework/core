<?php

namespace DigitalMarketingFramework\Core\Notification;

trait NotificationManagerAwareTrait
{
    protected NotificationManagerInterface $notificationManager;

    public function setNotificationManager(NotificationManagerInterface $notificationManager): void
    {
        $this->notificationManager = $notificationManager;
    }
}
