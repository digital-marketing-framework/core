<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Backend;

use DigitalMarketingFramework\Core\Backend\BackendManager;
use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Section\Section;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SectionMenuTest extends TestCase
{
    protected BackendManager $subject;

    protected function setUp(): void
    {
        $this->subject = new BackendManager($this->createMock(RegistryInterface::class));
    }

    #[Test]
    public function sectionsAreGroupedBySubTitle(): void
    {
        $this->subject->setSection(new Section('Global Settings', 'CORE', 'page.global-settings.edit', weight: 50));
        $this->subject->setSection(new Section('Distributor', 'DISTRIBUTOR', 'page.distributor.show-statistics', weight: 50));
        $this->subject->setSection(new Section('Integrations', 'CORE', 'page.integrations.overview', weight: 100));

        $menu = $this->subject->getSectionMenu(new Request('page.integrations.overview'));

        // The overview has no subtitle, so it leads on its own; groups follow in the order
        // their first section appeared, which keeps the weights meaningful.
        $this->assertSame(['', 'CORE', 'DISTRIBUTOR'], array_column($menu, 'label'));
        $this->assertSame(['Overview'], array_column($menu[0]['sections'], 'label'));
        $this->assertSame(['Global Settings', 'Integrations'], array_column($menu[1]['sections'], 'label'));
        $this->assertSame(['Distributor'], array_column($menu[2]['sections'], 'label'));
    }

    #[Test]
    public function theSectionOfTheRequestIsMarkedActive(): void
    {
        $this->subject->setSection(new Section('Global Settings', 'CORE', 'page.global-settings.edit'));
        $this->subject->setSection(new Section('Integrations', 'CORE', 'page.integrations.overview'));

        $menu = $this->subject->getSectionMenu(new Request('page.integrations.field-definitions'));

        // Any action of the section counts, not just the one the section links to.
        $this->assertSame([false, true], array_column($menu[1]['sections'], 'active'));
    }

    #[Test]
    public function disabledSectionsAreNotOffered(): void
    {
        $this->subject->setSection(new Section('Global Settings', 'CORE', 'page.global-settings.edit'));
        $this->subject->setSection(new class('Notifications', 'CORE', 'page.notifications.list') extends Section {
            public function enabled(RegistryInterface $registry): bool
            {
                return false;
            }
        });

        $menu = $this->subject->getSectionMenu(new Request('page.global-settings.edit'));

        $this->assertSame(['Global Settings'], array_column($menu[1]['sections'], 'label'));
        $this->assertSame(['global-settings'], array_keys($this->subject->getEnabledSections()));
        $this->assertCount(2, $this->subject->getAllSections());

        // Still found by name, so its own pages keep working when opened directly.
        $this->assertInstanceOf(Section::class, $this->subject->getSection('notifications'));
    }
}
