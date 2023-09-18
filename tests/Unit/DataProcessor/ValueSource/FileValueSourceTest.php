<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FileValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValueInterface;

class FileValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'file';

    protected const CLASS_NAME = FileValueSource::class;

    /** @test */
    public function fileValueSource(): void
    {
        $config = [
            FileValueSource::KEY_NAME => ['nameKey' => 'nameValue'],
            FileValueSource::KEY_PATH => ['pathKey' => 'pathValue'],
            FileValueSource::KEY_URL => ['urlKey' => 'urlValue'],
            FileValueSource::KEY_MIMETYPE => ['mimetypeKey' => 'mimetypeValue'],
        ];
        $this->dataProcessor
            ->method('processValue')
            ->withConsecutive(
                [$config[FileValueSource::KEY_NAME]],
                [$config[FileValueSource::KEY_PATH]],
                [$config[FileValueSource::KEY_URL]],
                [$config[FileValueSource::KEY_MIMETYPE]]
            )
            ->willReturnOnConsecutiveCalls('a', 'b', 'c', 'd');
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
