<?php

namespace DigitalMarketingFramework\Core\Model\ConfigurationDocument;

/**
 * Abstract base for migratable configuration documents.
 *
 * Provides common implementation for mutable state (status, relationships).
 * Subclasses implement identity and I/O specific to their storage mechanism.
 */
abstract class Migratable implements MigratableInterface
{
    /** @var array<string> */
    protected array $includes = [];

    /** @var array<string> */
    protected array $includedBy = [];

    protected bool $empty = false;

    protected bool $outdated = false;

    protected bool $hasOutdatedParents = false;

    protected bool $canMigrateIndividually = true;

    /** @var array<string, array{from: string, to: string, status: string, message: string}> */
    protected array $migrationInfo = [];

    // -- ItemInterface --

    public function getId(): int|string|null
    {
        return $this->getIdentifier();
    }

    public function setId(int|string $id): void
    {
        // Identifier is immutable, ignore
    }

    public function getLabel(): string
    {
        return $this->getName();
    }

    public function getDescription(): string
    {
        return '';
    }

    public function canHaveVariants(): bool
    {
        return false;
    }

    public function getBaseMigratableIdentifier(): ?string
    {
        return null;
    }

    public function isVariant(): bool
    {
        return $this->getBaseMigratableIdentifier() !== null;
    }

    // -- Relationships --

    public function getIncludes(): array
    {
        return $this->includes;
    }

    public function setIncludes(array $includes): void
    {
        $this->includes = $includes;
    }

    public function getIncludedBy(): array
    {
        return $this->includedBy;
    }

    public function setIncludedBy(array $includedBy): void
    {
        $this->includedBy = $includedBy;
    }

    // -- Migration status --

    public function isEmpty(): bool
    {
        return $this->empty;
    }

    public function setEmpty(bool $empty): void
    {
        $this->empty = $empty;
    }

    public function isOutdated(): bool
    {
        return $this->outdated;
    }

    public function setOutdated(bool $outdated): void
    {
        $this->outdated = $outdated;
    }

    public function hasOutdatedParents(): bool
    {
        return $this->hasOutdatedParents;
    }

    public function setHasOutdatedParents(bool $hasOutdatedParents): void
    {
        $this->hasOutdatedParents = $hasOutdatedParents;
    }

    public function canMigrateIndividually(): bool
    {
        return $this->canMigrateIndividually;
    }

    public function setCanMigrateIndividually(bool $canMigrateIndividually): void
    {
        $this->canMigrateIndividually = $canMigrateIndividually;
    }

    public function getMigrationInfo(): array
    {
        return $this->migrationInfo;
    }

    public function setMigrationInfo(array $migrationInfo): void
    {
        $this->migrationInfo = $migrationInfo;
    }
}
