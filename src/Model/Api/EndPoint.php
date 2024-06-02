<?php

namespace DigitalMarketingFramework\Core\Model\Api;

class EndPoint implements EndPointInterface
{
    public function __construct(
        protected string $name,
        protected bool $enabled,
        protected bool $pushEnabled,
        protected bool $pullEnabled,
        protected bool $disableContext,
        protected bool $allowContextOverride,
        protected bool $exposeToFrontend,
        protected string $configurationDocument,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getPushEnabled(): bool
    {
        return $this->pushEnabled;
    }

    public function setPushEnabled(bool $pushEnabled): void
    {
        $this->pushEnabled = $pushEnabled;
    }

    public function getPullEnabled(): bool
    {
        return $this->pullEnabled;
    }

    public function setPullEnabled(bool $pullEnabled): void
    {
        $this->pullEnabled = $pullEnabled;
    }

    public function getDisableContext(): bool
    {
        return $this->disableContext;
    }

    public function setDisableContext(bool $disableContext): void
    {
        $this->disableContext = $disableContext;
    }

    public function getAllowContextOverride(): bool
    {
        return $this->allowContextOverride;
    }

    public function setAllowContextOverride(bool $allowContextOverride): void
    {
        $this->allowContextOverride = $allowContextOverride;
    }

    public function getExposeToFrontend(): bool
    {
        return $this->exposeToFrontend;
    }

    public function setExposeToFrontend(bool $exposeToFrontend): void
    {
        $this->exposeToFrontend = $exposeToFrontend;
    }

    public function getConfigurationDocument(): string
    {
        return $this->configurationDocument;
    }

    public function setConfigurationDocument(string $configurationDocument): void
    {
        $this->configurationDocument = $configurationDocument;
    }
}
