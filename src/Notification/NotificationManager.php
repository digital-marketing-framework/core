<?php

namespace DigitalMarketingFramework\Core\Notification;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class NotificationManager implements NotificationManagerInterface, GlobalConfigurationAwareInterface
{
    use GlobalConfigurationAwareTrait;

    public const COMPONENT_SEPARATOR = ':';

    /**
     * @var ?array<NotificationChannelInterface>
     */
    protected ?array $channels = null;

    /**
     * @var array<string>
     */
    protected array $componentStack = [];

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    /**
     * @return array<NotificationChannelInterface>
     */
    protected function getChannels(): array
    {
        if ($this->channels === null) {
            $this->channels = $this->registry->getAllNotificationChannels();
        }

        return $this->channels;
    }

    protected function getComponent(string $component): string
    {
        if ($component !== '') {
            $this->pushComponent($component);
        }

        $result = $this->componentStack !== []
            ? implode(static::COMPONENT_SEPARATOR, $this->componentStack)
            : static::DEFAULT_COMPONENT;

        if ($component !== '') {
            $this->popComponent();
        }

        return $result;
    }

    public function getComponentDepth(): int
    {
        return count($this->componentStack);
    }

    public function pushComponent(string $component): int
    {
        $this->componentStack[] = $component;

        return $this->getComponentDepth() - 1;
    }

    public function popComponent(int $toLevel = -1): void
    {
        if ($toLevel === -1) {
            $toLevel = count($this->componentStack) - 1;
        }

        while (count($this->componentStack) > $toLevel) {
            array_pop($this->componentStack);
        }
    }

    public function enabled(): bool
    {
        return $this->globalConfiguration->get('core')[CoreGlobalConfigurationSchema::KEY_NOTIFICATIONS][CoreGlobalConfigurationSchema::KEY_NOTIFICATIONS_ENABLED]
            ?? CoreGlobalConfigurationSchema::DEFAULT_NOTIFICATIONS_ENABLED;
    }

    public function notify(
        string $title,
        string $message = '',
        mixed $details = null,
        string $component = '',
        int $level = NotificationManagerInterface::LEVEL_NOTICE,
    ): void {
        if (!$this->enabled()) {
            return;
        }

        $fullComponent = $this->getComponent($component);

        foreach ($this->getChannels() as $channel) {
            if (!$channel->accept($fullComponent, $level)) {
                continue;
            }

            $channel->notify($title, $message, $details, $component, $level);
        }
    }
}
