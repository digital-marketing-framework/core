<?php

namespace DigitalMarketingFramework\Core\Notification;

interface NotificationManagerInterface
{
    public const LEVEL_NOTICE = 0;

    public const LEVEL_WARNING = 1;

    public const LEVEL_ERROR = 2;

    public const DEFAULT_COMPONENT = 'global';

    public function notify(
        string $title,
        string $message = '',
        mixed $details = null,
        string $component = '',
        int $level = NotificationManagerInterface::LEVEL_NOTICE
    ): void;

    public function pushComponent(string $component): void;

    public function popComponent(): void;
}
