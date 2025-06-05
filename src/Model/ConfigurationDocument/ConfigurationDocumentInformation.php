<?php

namespace DigitalMarketingFramework\Core\Model\ConfigurationDocument;

use DigitalMarketingFramework\Core\Model\Backend\ItemInterface;

class ConfigurationDocumentInformation implements ItemInterface
{
    /**
     * @param array<string> $includes
     */
    public function __construct(
        protected string $id,
        protected string $shortId,
        protected string $name,
        protected bool $readonly,
        protected array $includes,
        protected string $content = '',
    ) {
    }

    public function getLabel(): string
    {
        return $this->getName();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getShortId(): string
    {
        return $this->shortId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * @return array<string>
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
