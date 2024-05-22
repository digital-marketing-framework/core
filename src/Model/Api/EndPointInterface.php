<?php

namespace DigitalMarketingFramework\Core\Model\Api;

interface EndPointInterface
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getEnabled(): bool;

    public function setEnabled(bool $enabled): void;

    public function getPushEnabled(): bool;

    public function setPushEnabled(bool $pushEnabled): void;

    public function getPullEnabled(): bool;

    public function setPullEnabled(bool $pullEnabled): void;

    public function getDisableContext(): bool;

    public function setDisableContext(bool $disableContext): void;

    public function getAllowContextOverride(): bool;

    public function setAllowContextOverride(bool $allowContextOverride): void;

    public function getExposeToFrontend(): bool;

    public function setExposeToFrontend(bool $exposeToFrontend): void;

    public function getConfigurationDocument(): string;

    public function setConfigurationDocument(string $configurationDocument): void;
}
