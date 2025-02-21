<?php

namespace DigitalMarketingFramework\Core\Alert;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class AlertManager implements AlertManagerInterface
{
    /**
     * @var ?array<AlertHandlerInterface>
     */
    protected ?array $alertHandlers = null;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    /**
     * @return array<AlertHandlerInterface>
     */
    protected function getAllAlertHandlers(): array
    {
        if ($this->alertHandlers === null) {
            $this->alertHandlers = $this->registry->getAllAlertHandlers();
        }

        return $this->alertHandlers;
    }

    public function getAllAlerts(): array
    {
        $alerts = [];
        foreach ($this->getAllAlertHandlers() as $alertHandler) {
            foreach ($alertHandler->getAlerts() as $alert) {
                $alerts[] = $alert;
            }
        }

        return $alerts;
    }
}
