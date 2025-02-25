<?php

namespace DigitalMarketingFramework\Core\Alert;

use DigitalMarketingFramework\Core\Model\Alert\Alert;
use DigitalMarketingFramework\Core\Model\Alert\AlertInterface;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

abstract class AlertHandler extends Plugin implements AlertHandlerInterface
{
    public function __construct(
        string $keyword,
        protected RegistryInterface $registry,
    ) {
        parent::__construct($keyword);
    }

    protected function createAlert(
        string $content,
        ?string $title = null,
        int $type = AlertInterface::TYPE_INFO,
    ): AlertInterface {
        return new Alert($this->getKeyword(), $content, $title, $type);
    }
}
