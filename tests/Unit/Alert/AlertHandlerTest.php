<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Alert;

use DigitalMarketingFramework\Core\Alert\AlertHandler;
use DigitalMarketingFramework\Core\Model\Alert\AlertInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AlertHandlerTest extends TestCase
{
    protected function createSubject(): AlertHandler
    {
        return new class('myHandler', $this->createMock(RegistryInterface::class)) extends AlertHandler {
            public function getAlerts(): array
            {
                return [
                    $this->createAlert(
                        'Something went wrong.',
                        'My Title',
                        AlertInterface::TYPE_ERROR,
                        [
                            $this->createAction('Show list', 'page.my-section.list', ['filters' => ['status' => 'failed']], 'link'),
                            $this->createAction('Show settings', 'page.my-section.settings'),
                        ]
                    ),
                    $this->createAlert('Just so you know.'),
                ];
            }
        };
    }

    #[Test]
    public function createdAlertCarriesItsActions(): void
    {
        [$alert] = $this->createSubject()->getAlerts();

        $this->assertSame('myHandler', $alert->getSource());
        $this->assertSame('Something went wrong.', $alert->getContent());
        $this->assertSame('My Title', $alert->getTitle());
        $this->assertSame(AlertInterface::TYPE_ERROR, $alert->getType());

        $actions = $alert->getActions();
        $this->assertCount(2, $actions);

        $this->assertSame('Show list', $actions[0]->getLabel());
        $this->assertSame('page.my-section.list', $actions[0]->getRoute());
        $this->assertSame(['filters' => ['status' => 'failed']], $actions[0]->getArguments());
        $this->assertSame('link', $actions[0]->getIcon());

        $this->assertSame('Show settings', $actions[1]->getLabel());
        $this->assertSame('page.my-section.settings', $actions[1]->getRoute());
        $this->assertSame([], $actions[1]->getArguments());
        $this->assertSame('', $actions[1]->getIcon());
    }

    #[Test]
    public function createdAlertWithoutActionsHasNone(): void
    {
        [, $alert] = $this->createSubject()->getAlerts();

        $this->assertSame(AlertInterface::TYPE_INFO, $alert->getType());
        $this->assertSame([], $alert->getActions());
    }
}
