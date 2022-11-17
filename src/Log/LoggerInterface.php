<?php

namespace DigitalMarketingFramework\Core\Log;

interface LoggerInterface
{
    public function debug(string $msg);
    public function info(string $msg);
    public function warning(string $msg);
    public function error(string $msg);
}
