<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Alert;

use DigitalMarketingFramework\Core\Model\Alert\Alert;
use DigitalMarketingFramework\Core\Model\Alert\AlertAction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AlertTest extends TestCase
{
    #[Test]
    public function hasNoActionsByDefault(): void
    {
        $alert = new Alert('source', 'content');

        $this->assertSame([], $alert->getActions());
    }

    #[Test]
    public function keepsActionsInTheOrderTheyWereGiven(): void
    {
        $first = new AlertAction('First', 'page.section.first');
        $second = new AlertAction('Second', 'page.section.second');
        $third = new AlertAction('Third', 'page.section.third');

        $alert = new Alert('source', 'content', actions: [$first, $second]);
        $alert->addAction($third);

        $this->assertSame([$first, $second, $third], $alert->getActions());
    }

    #[Test]
    public function actionDefaults(): void
    {
        $action = new AlertAction('Label', 'page.section.action');

        $this->assertSame('Label', $action->getLabel());
        $this->assertSame('page.section.action', $action->getRoute());
        $this->assertSame([], $action->getArguments());
        $this->assertSame('', $action->getIcon());
    }

    #[Test]
    public function actionWithArgumentsAndIcon(): void
    {
        $arguments = ['filters' => ['status' => ['failed' => 1]]];
        $action = new AlertAction('Label', 'page.section.action', $arguments, 'link');

        $this->assertSame($arguments, $action->getArguments());
        $this->assertSame('link', $action->getIcon());
    }
}
