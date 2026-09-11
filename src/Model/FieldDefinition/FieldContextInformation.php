<?php

namespace DigitalMarketingFramework\Core\Model\FieldDefinition;

use DigitalMarketingFramework\Core\Model\ItemInterface;

/**
 * One row of the field definition list: a context, and every source its fields come from.
 *
 * The list is over contexts rather than files, so that a context a plugin only declares in code
 * can still be found and extended. The sources are listed with it because each is edited on its
 * own, and because a reader otherwise cannot tell where a context's fields come from.
 */
class FieldContextInformation implements ItemInterface
{
    /**
     * @param array<FieldContextSource> $sources where the fields come from, lowest precedence first
     * @param int $fieldCount fields the context ends up with once every source is folded together
     * @param ?string $integration the integration this context belongs to, or null when it belongs to none
     */
    public function __construct(
        protected int|string|null $id,
        protected string $label,
        protected array $sources,
        protected int $fieldCount,
        protected ?string $integration = null,
        protected string $type = '',
        protected string $route = '',
        protected bool $firstOfGroup = true,
        protected string $content = '',
    ) {
    }

    public function getIntegration(): ?string
    {
        return $this->integration;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getFirstOfGroup(): bool
    {
        return $this->firstOfGroup;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function setId(int|string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return array<FieldContextSource>
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    public function getFieldCount(): int
    {
        return $this->fieldCount;
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
