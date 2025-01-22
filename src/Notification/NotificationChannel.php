<?php

namespace DigitalMarketingFramework\Core\Notification;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Notification\Schema\GlobalNotificationChannelConfigurationSchema;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class NotificationChannel implements NotificationChannelInterface, GlobalConfigurationAwareInterface, LoggerAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use LoggerAwareTrait;

    /**
     * @var ?array<string,mixed>
     */
    protected ?array $configuration = null;

    public function __construct(
        protected string $keyword,
        protected RegistryInterface $registry,
    ) {
    }

    abstract public function notify(
        string $title,
        string $message,
        mixed $details,
        string $component,
        int $level,
    ): void;

    abstract protected function getConfigPackageKey(): string;

    protected function levelToString(int $level): string
    {
        return match ($level) {
            NotificationManagerInterface::LEVEL_NOTICE => 'NOTICE',
            NotificationManagerInterface::LEVEL_WARNING => 'WARNING',
            NotificationManagerInterface::LEVEL_ERROR => 'ERROR',
            default => throw new DigitalMarketingFrameworkException(sprintf('Unknown notification level "%s"', $level)),
        };
    }

    protected function getBody(string $title, string $message, mixed $details, string $component, int $level): string
    {
        $body = $title . PHP_EOL;

        $body .= 'component: ' . $component . PHP_EOL;

        $body .= $this->levelToString($level) . PHP_EOL;

        $body .= $message . PHP_EOL;

        if ($details !== null) {
            $body .= PHP_EOL . '===' . PHP_EOL . print_r($details, true) . PHP_EOL;
        }

        return $body;
    }

    protected function getHtmlBody(string $title, string $message, mixed $details, string $component, int $level): string
    {
        $body = '<h1>' . $title . '</h1>' . PHP_EOL;

        $body .= '<p>component: ' . $component . '</p>' . PHP_EOL;

        $body .= '<p>' . $this->levelToString($level) . '</p>' . PHP_EOL;

        $body .= $message . PHP_EOL;

        if ($details !== null) {
            $body .= PHP_EOL . '<pre>' . print_r($details, true) . '</pre>' . PHP_EOL;
        }

        return $body;
    }

    /**
     * @return array<string,mixed>
     */
    protected function getConfiguration(): array
    {
        if ($this->configuration === null) {
            $this->configuration = $this->globalConfiguration->get($this->getConfigPackageKey());
        }

        return $this->configuration;
    }

    protected function checkAllowList(string $list, string $item): bool
    {
        $allowList = GeneralUtility::castValueToArray($list);
        foreach ($allowList as $allowed) {
            $regexpAllowed = str_replace('*', '.*', (string)$allowed);
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

        return $this->checkAllowList($list, $component);
    }

    public function acceptLevel(int $level): bool
    {
        $config = $this->getConfiguration();
        $list = $config[GlobalNotificationChannelConfigurationSchema::KEY_LEVELS] ?? GlobalNotificationChannelConfigurationSchema::DEFAULT_LEVELS;

        return $this->checkAllowList($list, (string)$level);
    }

    public function accept(string $component, int $level): bool
    {
        return $this->enabled() && $this->acceptComponent($component) && $this->acceptLevel($level);
    }
}
