<?php

namespace DigitalMarketingFramework\Core\Model\FieldDefinition;

/**
 * One place a field context gets fields from: a definition file in some folder, or the code
 * that declared the context.
 *
 * A context is the sum of its sources, each correcting the ones before it, and every action in
 * the backend works on one of them rather than on the context as a whole.
 */
class FieldContextSource
{
    /**
     * @param ?string $folder the folder this file lives in, or null for fields declared in code
     * @param bool $stored whether a file actually exists; false for the storage folder that
     *                     could hold one but does not yet
     */
    public function __construct(
        protected ?string $folder,
        protected int $fieldCount,
        protected bool $stored,
        protected bool $writable,
    ) {
    }

    public function getFolder(): ?string
    {
        return $this->folder;
    }

    public function getDeclaredInCode(): bool
    {
        return $this->folder === null;
    }

    public function getFieldCount(): int
    {
        return $this->fieldCount;
    }

    public function getStored(): bool
    {
        return $this->stored;
    }

    public function getWritable(): bool
    {
        return $this->writable;
    }
}
