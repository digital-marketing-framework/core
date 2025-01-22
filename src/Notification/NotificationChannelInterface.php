<?php

namespace DigitalMarketingFramework\Core\Notification;

use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface NotificationChannelInterface extends PluginInterface
{
    public function notify(
        string $title,
        string $message,
        mixed $details,
        string $component,
        int $level,
    ): void;

    public function enabled(): bool;

    public function acceptComponent(string $component): bool;

    public function acceptLevel(int $level): bool;

    public function accept(string $component, int $level): bool;
}
