<?php

namespace DigitalMarketingFramework\Core\FieldDefinition\Storage;

/**
 * Field definition files, one per field context per folder.
 *
 * Folders form a precedence order and a context may have a file in several of them: a
 * package ships the fields it knows about, a site package adds the ones this project uses.
 * Those are layers, not replacements, so the storage addresses files by context *and*
 * folder and leaves combining them to the caller.
 *
 * Content is handled as raw text so that parsing stays out of storage.
 */
interface FieldDefinitionStorageInterface
{
    /**
     * Every context that has a file in any folder, without duplicates.
     *
     * @return array<string>
     */
    public function getContextIdentifiers(): array;

    /**
     * Folders holding a file for this context, lowest precedence first.
     *
     * @return array<string>
     */
    public function getFolders(string $contextIdentifier): array;

    public function exists(string $contextIdentifier, ?string $folder = null): bool;

    /**
     * Reads one layer, or the highest-precedence one when no folder is given.
     */
    public function getContent(string $contextIdentifier, ?string $folder = null): ?string;

    /**
     * Writes one layer. Without a folder the configured writable folder is used — never a
     * folder the content happens to already exist in, because "may write into packages" does
     * not say *which* package was meant.
     */
    public function setContent(string $contextIdentifier, string $content, ?string $folder = null): void;

    public function delete(string $contextIdentifier, ?string $folder = null): void;

    /**
     * Folders that can be written to on this system, lowest precedence first. Normally just
     * the configured writable folder; package folders join it only where saving to package
     * paths has been allowed, which is a development-machine setting.
     *
     * @return array<string>
     */
    public function getWritableFolders(): array;

    /**
     * The one folder configured for this system to write to, or an empty string when none is.
     *
     * Distinct from getWritableFolders(), which on a system that allows writing to package
     * paths is every folder there is. This one is where an override belongs.
     */
    public function getStorageFolder(): string;

    /**
     * Called once the storage is registered, to protect a folder that already exists. A folder
     * that does not is protected when it is created.
     */
    public function initializeStorage(): void;

    /**
     * Whether a definition could be stored, which is not the same as the folder being there:
     * the folder appears with the first definition.
     */
    public function isStorageReady(): bool;

    /**
     * Whether the storage folder can be fetched over the web.
     */
    public function isStoragePubliclyAccessible(): bool;

    /**
     * Whether it is in fact unreachable — either because the storage is not public, or because
     * an access file is in place and this server reads them.
     */
    public function isStorageProtected(): bool;

    public function isReadOnly(string $contextIdentifier, ?string $folder = null): bool;
}
