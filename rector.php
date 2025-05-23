<?php

declare(strict_types=1);

use Mediatis\CodingStandards\Php\RectorSetup;
use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    RectorSetup::setup($rectorConfig, __DIR__, PhpVersion::PHP_82);
};
