<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FileValueSource;
use DigitalMarketingFramework\Core\Model\Data\Value\FileValueInterface;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueSource\FileValueSource
 */
class FileValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'file';

    /** @test */
    public function fileValueSource(): void
    {
        $config = [
            FileValueSource::KEY_NAME => $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'a'], 'constant'),
            FileValueSource::KEY_PATH => $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'b'], 'constant'),
            FileValueSource::KEY_URL => $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'c'], 'constant'),
            FileValueSource::KEY_MIMETYPE => $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'd'], 'constant'),
        ];

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
