<?php

declare(strict_types=1);

use Mediatis\CodingStandards\Php\RectorSetup;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    RectorSetup::setup($rectorConfig, __DIR__);
};
