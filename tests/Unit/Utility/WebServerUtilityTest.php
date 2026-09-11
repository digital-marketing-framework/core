<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Utility;

use DigitalMarketingFramework\Core\Utility\WebServerUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WebServerUtilityTest extends TestCase
{
    protected mixed $originalServerSoftware = null;

    protected function setUp(): void
    {
        $this->originalServerSoftware = $_SERVER['SERVER_SOFTWARE'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->originalServerSoftware === null) {
            unset($_SERVER['SERVER_SOFTWARE']);
        } else {
            $_SERVER['SERVER_SOFTWARE'] = $this->originalServerSoftware;
        }
    }

    /**
     * @return array<string,array{?string,bool}>
     */
    public static function serverProvider(): array
    {
        return [
            'apache' => ['apache', true],
            'apache with version' => ['Apache/2.4.62 (Debian)', true],
            'nginx' => ['nginx/1.24.0', false],
            'iis' => ['Microsoft-IIS/10.0', false],
            'litespeed' => ['LiteSpeed', false],
            // No server at all, so nothing reads an access file into effect. The folder is
            // protected by the next web request instead.
            'cli' => [null, false],
        ];
    }

    #[Test]
    #[DataProvider('serverProvider')]
    public function onlyApacheReadsAccessFiles(?string $serverSoftware, bool $expected): void
    {
        if ($serverSoftware === null) {
            unset($_SERVER['SERVER_SOFTWARE']);
        } else {
            $_SERVER['SERVER_SOFTWARE'] = $serverSoftware;
        }

        $this->assertSame($expected, WebServerUtility::supportsAccessFile());
    }
}
