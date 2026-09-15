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
        string $environment = '',
        mixed $details = null,
        string $component = '',
        int $level = NotificationManagerInterface::LEVEL_NOTICE,
    ): void;

    /**
     * Whether notifications are switched on at all. Reads the global configuration directly, so
     * it can be asked while the registry is still being set up.
     */
    public function enabled(): bool;

    public function getComponentDepth(): int;

    public function pushComponent(string $component): int;

    public function popComponent(int $toLevel = -1): void;
}
