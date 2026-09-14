<?php

namespace DigitalMarketingFramework\Core\Alert;

use DigitalMarketingFramework\Core\Model\Alert\Alert;
use DigitalMarketingFramework\Core\Model\Alert\AlertAction;
use DigitalMarketingFramework\Core\Model\Alert\AlertActionInterface;
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

    /**
     * @param array<AlertActionInterface> $actions
     */
    protected function createAlert(
        string $content,
        ?string $title = null,
        int $type = AlertInterface::TYPE_INFO,
        array $actions = [],
    ): AlertInterface {
        return new Alert($this->getKeyword(), $content, $title, $type, $actions);
    }

    /**
     * @param array<string,mixed> $arguments
     */
    protected function createAction(
        string $label,
        string $route,
        array $arguments = [],
        string $icon = '',
    ): AlertActionInterface {
        return new AlertAction($label, $route, $arguments, $icon);
    }
}
