<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Notification;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\Notification\NotificationChannelInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManager;
use DigitalMarketingFramework\Core\Notification\NotificationManagerInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotificationManager::class)]
class NotificationManagerTest extends TestCase
{
    protected RegistryInterface&MockObject $registry;

    protected GlobalConfigurationInterface&MockObject $globalConfiguration;

    /**
     * @var array<NotificationChannelInterface&MockObject>
     */
    protected array $notificationChannels = [];

    protected bool $notificationSystemEnabled = false;

    protected NotificationManager $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = $this->createMock(RegistryInterface::class);
        $this->registry->method('getAllNotificationChannels')->willReturnCallback(fn () => $this->notificationChannels);

        $this->globalConfiguration = $this->createMock(GlobalConfigurationInterface::class);
        $this->globalConfiguration->method('get')->with('core')->willReturnCallback(fn () => [
            CoreGlobalConfigurationSchema::KEY_NOTIFICATIONS => [
                CoreGlobalConfigurationSchema::KEY_NOTIFICATIONS_ENABLED => $this->notificationSystemEnabled,
            ],
        ]);

        $this->subject = new NotificationManager($this->registry);
        $this->subject->setGlobalConfiguration($this->globalConfiguration);
    }

    protected function addChannel(callable|bool $acceptCallback): NotificationChannelInterface&MockObject
    {
        if (is_bool($acceptCallback)) {
            $acceptCallbackReturnValue = $acceptCallback;
            $acceptCallback = (static fn () => $acceptCallbackReturnValue);
        }

        $channel = $this->createMock(NotificationChannelInterface::class);
        $channel->method('accept')->willReturnCallback($acceptCallback);

        $this->notificationChannels[] = $channel;

        return $channel;
    }

    #[Test]
    public function notificationsDisabled(): void
    {
        $this->notificationSystemEnabled = false;

        $channel = $this->addChannel(true);
        $channel->expects($this->never())->method('notify');

        $this->subject->notify(
            'my-title',
            'my-message',
            'my-environment',
            'my-details',
            'my-component'
        );
    }

    #[Test]
    public function notificationChannelDisabled(): void
    {
        $this->notificationSystemEnabled = true;

        $channel = $this->addChannel(false);
        $channel->expects($this->never())->method('notify');

        $this->subject->notify(
            'my-title',
            'my-message',
            'my-environment',
            'my-details',
            'my-component'
        );
    }

    #[Test]
    public function notificationChannelEnabled(): void
    {
        $this->notificationSystemEnabled = true;

        $channel = $this->addChannel(true);
        $channel->expects($this->once())->method('notify')->with(
            'my-environment',
            'my-title',
            'my-message',
            'my-details',
            'my-component'
        );

        $this->subject->notify(
            'my-title',
            'my-message',
            'my-environment',
            'my-details',
            'my-component',
            NotificationManagerInterface::LEVEL_NOTICE
        );
    }

    #[Test]
    public function notificationChannelEnabledWithDefaultComponent(): void
    {
        $this->notificationSystemEnabled = true;

        $channel = $this->addChannel(true);
        $channel->expects($this->once())->method('notify')->with(
            'my-environment',
            'my-title',
            'my-message',
            'my-details',
            NotificationManagerInterface::DEFAULT_COMPONENT
        );

        $this->subject->notify(
            'my-title',
            'my-message',
            'my-environment',
            'my-details'
        );
    }

    /**
     * @return array<array{string,array<string>,string}>
     */
    public static function componentDataProvider(): array
    {
        return [
            [
                '',
                [],
                NotificationManagerInterface::DEFAULT_COMPONENT,
            ],
            [
                'my-component',
                [],
                'my-component',
            ],
            [
                '',
                ['my-pushed-component'],
                'my-pushed-component',
            ],
            [
                'my-component',
                ['my-pushed-component'],
                'my-pushed-component:my-component',
            ],
            [
                '',
                ['my-pushed-component', 'my-pushed-component-2'],
                'my-pushed-component:my-pushed-component-2',
            ],
            [
                'my-component',
                ['my-pushed-component', 'my-pushed-component-2'],
                'my-pushed-component:my-pushed-component-2:my-component',
            ],
        ];
    }

    /**
     * @param array<string> $pushedComponents
     */
    #[Test]
    #[DataProvider('componentDataProvider')]
    public function componentsStack(string $component, array $pushedComponents, string $expectedFullComponent): void
    {
        $this->notificationSystemEnabled = true;

        $channel = $this->addChannel(true);
        $channel->expects($this->once())->method('notify')->with(
            'my-environment',
            'my-title',
            'my-message',
            'my-details',
            $expectedFullComponent
        );

        foreach ($pushedComponents as $pushedComponent) {
            $this->subject->pushComponent($pushedComponent);
        }

        $this->subject->notify(
            'my-title',
            'my-message',
            'my-environment',
            'my-details',
            $component
        );

        foreach ($pushedComponents as $pushedComponent) {
            $this->subject->popComponent();
        }
    }

    #[Test]
    public function componentStackSavedDepth(): void
    {
        $this->notificationSystemEnabled = true;

        $channel = $this->addChannel(true);
        $channel->expects($this->once())->method('notify')->with(
            'my-environment',
            'my-title',
            'my-message',
            'my-details',
            'c1:c6:c7'
        );

        $this->subject->pushComponent('c1');
        $this->subject->pushComponent('c2');

        $componentLevel = $this->subject->pushComponent('c3');

        $this->subject->pushComponent('c4');
        $this->subject->pushComponent('c5');

        $this->subject->popComponent($componentLevel);

        $this->subject->popComponent();

        $this->subject->pushComponent('c6');

        $this->subject->notify(
            'my-title',
            'my-message',
            'my-environment',
            'my-details',
            'c7'
        );
    }

    // TODO tests for channel and component whitelist patterns
}
