<?php

namespace DigitalMarketingFramework\Core\Log;

interface LoggerInterface
{
    public function debug(string $msg): void;

    public function info(string $msg): void;

    public function warning(string $msg): void;

    public function error(string $msg): void;
}
