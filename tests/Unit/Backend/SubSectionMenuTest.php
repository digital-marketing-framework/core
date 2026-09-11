<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Backend;

use DigitalMarketingFramework\Core\Backend\BackendManager;
use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Section\SubSection;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SubSectionMenuTest extends TestCase
{
    protected BackendManager $subject;

    protected function setUp(): void
    {
        $this->subject = new BackendManager($this->createMock(RegistryInterface::class));
    }

    protected function register(SubSection ...$subSections): void
    {
        foreach ($subSections as $subSection) {
            $this->subject->addSubSection($subSection);
        }
    }

    #[Test]
    public function subSectionsAreListedByWeightAndMarkedByAction(): void
    {
        $this->register(
            new SubSection('Maintenance', 'page.configuration-document.maintenance', weight: 200),
            new SubSection('List', 'page.configuration-document.list', weight: 100),
        );

        $menu = $this->subject->getSubSectionMenu(new Request('page.configuration-document.maintenance'));

        $this->assertSame(['List', 'Maintenance'], array_column($menu, 'label'));
        $this->assertSame([false, true], array_column($menu, 'active'));
    }

    #[Test]
    public function anAdditionalActionKeepsItsSubSectionMarked(): void
    {
        $this->register(
            new SubSection('List', 'page.configuration-document.list'),
            new SubSection('Maintenance', 'page.configuration-document.maintenance', ['maintenance-edit']),
        );

        // Editing a maintenance record is still the maintenance subsection, which is what the
        // templates used to say by hand.
        $menu = $this->subject->getSubSectionMenu(new Request('page.configuration-document.maintenance-edit'));

        $this->assertSame([false, true], array_column($menu, 'active'));
    }

    #[Test]
    public function oneSubSectionIsNoChoiceAndRendersNothing(): void
    {
        $this->register(new SubSection('List', 'page.configuration-document.list'));

        $this->assertSame([], $this->subject->getSubSectionMenu(new Request('page.configuration-document.list')));
    }

    #[Test]
    public function aSectionWithoutSubSectionsHasNoMenu(): void
    {
        $this->register(new SubSection('List', 'page.configuration-document.list'));

        // Registration is per section, so another section is unaffected by what this one has.
        $this->assertSame([], $this->subject->getSubSectionMenu(new Request('page.global-settings.edit')));
    }

    #[Test]
    public function registeringTheSameSubSectionTwiceDoesNotDuplicateIt(): void
    {
        $this->register(
            new SubSection('List', 'page.configuration-document.list'),
            new SubSection('Maintenance', 'page.configuration-document.maintenance'),
            new SubSection('Maintenance', 'page.configuration-document.maintenance'),
        );

        $this->assertCount(2, $this->subject->getSubSectionMenu(new Request('page.configuration-document.list')));
    }
}
