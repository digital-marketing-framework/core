<?php

namespace DigitalMarketingFramework\Core\Plugin;

use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;

abstract class Plugin implements PluginInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const WEIGHT = 10;

    public function __construct(
        protected string $keyword,
    ) {
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function getWeight(): int
    {
        return static::WEIGHT;
    }
}
