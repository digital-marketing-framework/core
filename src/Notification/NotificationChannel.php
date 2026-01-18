<?php

namespace DigitalMarketingFramework\Core\Notification;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Notification\GlobalConfiguration\Settings\NotificationChannelSettings;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

abstract class NotificationChannel extends Plugin implements NotificationChannelInterface, GlobalConfigurationAwareInterface, LoggerAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use LoggerAwareTrait;

    public function __construct(
        protected string $keyword,
        protected RegistryInterface $registry,
    ) {
        parent::__construct($keyword);
    }

    abstract public function notify(
        string $environment,
        string $title,
        string $message,
        mixed $details,
        string $component,
        int $level,
    ): void;

    abstract protected function getConfiguration(): NotificationChannelSettings;

    protected function levelToString(int $level): string
    {
        return match ($level) {
            NotificationManagerInterface::LEVEL_NOTICE => 'NOTICE',
            NotificationManagerInterface::LEVEL_WARNING => 'WARNING',
            NotificationManagerInterface::LEVEL_ERROR => 'ERROR',
            default => throw new DigitalMarketingFrameworkException(sprintf('Unknown notification level "%s"', $level)),
        };
    }

    protected function getBody(string $environment, string $title, string $message, mixed $details, string $component, int $level): string
    {
        $body = 'Title: ' . $title . PHP_EOL;
        $body .= 'Environment: ' . $environment . PHP_EOL;
        $body .= 'Component: ' . $component . PHP_EOL;
        $body .= 'Level: ' . $this->levelToString($level) . PHP_EOL;
        $body .= 'Message: ' . $message . PHP_EOL;

        if ($details !== null) {
            $body .= PHP_EOL . '===' . PHP_EOL . print_r($details, true) . PHP_EOL;
        }

        return $body;
    }

    protected function getHtmlBody(string $environment, string $title, string $message, mixed $details, string $component, int $level): string
    {
        $body = '<div style="font-family: monospace">';
        $body .= '<h1>' . $title . '</h1>' . PHP_EOL;
        $body .= '<p>Environment: ' . $environment . '</p>' . PHP_EOL;
        $body .= '<p>Component: ' . $component . '</p>' . PHP_EOL;
        $body .= '<p>Level: ' . $this->levelToString($level) . '</p>' . PHP_EOL;
        $body .= '<p>Message: ' . nl2br($message) . '</p>' . PHP_EOL;

        if ($details !== null) {
            $body .= PHP_EOL . '<pre>' . print_r($details, true) . '</pre>' . PHP_EOL;
        }

        return $body . '</div>';
    }

    protected function checkAllowList(string $list, string $item): bool
    {
        $allowList = GeneralUtility::castValueToArray($list);
        foreach ($allowList as $allowed) {
            $regexpAllowed = str_replace('*', '.*', (string)$allowed);
            if (preg_match('/^' . $regexpAllowed . '/', $item)) {
                return true;
            }
        }

        return false;
    }

    public function enabled(): bool
    {
        return $this->getConfiguration()->enabled();
    }

    public function acceptComponent(string $component): bool
    {
        $list = $this->getConfiguration()->getComponents();

        return $this->checkAllowList($list, $component);
    }

    public function acceptLevel(int $level): bool
    {
        $list = $this->getConfiguration()->getLevels();

        return $this->checkAllowList($list, (string)$level);
    }

    public function accept(string $component, int $level): bool
    {
        return $this->enabled() && $this->acceptComponent($component) && $this->acceptLevel($level);
    }
}
