<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FileValueSource;
use DigitalMarketingFramework\Core\FileStorage\FileStorageInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValueInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @extends ValueSourceTestBase<FileValueSource>
 */
class FileValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'file';

    protected const CLASS_NAME = FileValueSource::class;

    protected FileStorageInterface&MockObject $fileStorage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileStorage = $this->createMock(FileStorageInterface::class);
    }

    protected function processObjectAwareness(): void
    {
        parent::processObjectAwareness();
        $this->subject->setFileStorage($this->fileStorage);
    }

    #[Test]
    public function fileValueSource(): void
    {
        $config = [
            FileValueSource::KEY_NAME => ['nameKey' => 'nameValue'],
            FileValueSource::KEY_PATH => ['pathKey' => 'pathValue'],
            FileValueSource::KEY_URL => ['urlKey' => 'urlValue'],
            FileValueSource::KEY_MIMETYPE => ['mimetypeKey' => 'mimetypeValue'],
        ];

        $this->withConsecutiveWillReturn($this->dataProcessor, 'processValue', [
            [$config[FileValueSource::KEY_NAME]],
            [$config[FileValueSource::KEY_PATH]],
            [$config[FileValueSource::KEY_URL]],
            [$config[FileValueSource::KEY_MIMETYPE]],
        ], [
            'a', 'b', 'c', 'd',
        ]);

        $this->fileStorage->method('fileExists')->with('b')->willReturn(false);

        /** @var FileValueInterface $output */
        $output = $this->processValueSource($config);
        $this->assertInstanceOf(FileValueInterface::class, $output);
        $this->assertEquals('a', $output->getFileName());
        $this->assertEquals('b', $output->getRelativePath());
        $this->assertEquals('c', $output->getPublicUrl());
        $this->assertEquals('d', $output->getMimeType());
    }

    // TODO build test with existing file so that real file attributes can be taken into account
}
