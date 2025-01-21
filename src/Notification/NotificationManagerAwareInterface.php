<?php

namespace DigitalMarketingFramework\Core\Notification;

interface NotificationManagerAwareInterface
{
    public function setNotificationManager(NotificationManagerInterface $notificationManager): void;
}
