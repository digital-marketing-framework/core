<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FileValueSource;
use DigitalMarketingFramework\Core\FileStorage\FileStorageInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValueInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(FileValueSource::class)]
class FileValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'file';

    protected FileStorageInterface&MockObject $fileStorage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileStorage = $this->createMock(FileStorageInterface::class);
        $this->registry->setFileStorage($this->fileStorage);
    }

    #[Test]
    public function fileValueSource(): void
    {
        $config = [
            FileValueSource::KEY_NAME => $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'),
            FileValueSource::KEY_PATH => $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'b'], 'constant'),
            FileValueSource::KEY_URL => $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'c'], 'constant'),
            FileValueSource::KEY_MIMETYPE => $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'd'], 'constant'),
        ];

        $this->fileStorage->method('fileExists')->with('b')->willReturn(false);

        /** @var FileValueInterface $output */
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        $this->assertInstanceOf(FileValueInterface::class, $output);
        $this->assertEquals('a', $output->getFileName());
        $this->assertEquals('b', $output->getRelativePath());
        $this->assertEquals('c', $output->getPublicUrl());
        $this->assertEquals('d', $output->getMimeType());
    }

    // TODO build test with existing file so that real file attributes can be taken into account
}
