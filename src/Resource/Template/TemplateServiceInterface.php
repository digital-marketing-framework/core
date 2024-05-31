<?php

namespace DigitalMarketingFramework\Core\Resource\Template;

interface TemplateServiceInterface
{
    public function addTemplateFolder(string $identifier, int $priority = 100): void;

    public function addPartialFolder(string $identifier, int $priority = 100): void;

    /**
     * Returns all registered template folder identifiers.
     *
     * @return array<string>
     */
    public function getTemplateFolders(): array;

    /**
     * Returns all registered partial folder identifiers.
     *
     * @return array<string>
     */
    public function getPartialFolders(): array;

    /**
     * Returns all registered template folder system paths.
     *
     * @return array<string>
     */
    public function getTemplateFolderPaths(): array;

    /**
     * Returns all registered partial folder system paths.
     *
     * @return array<string>
     */
    public function getPartialFolderPaths(): array;

    /**
     * @param string|array<string> $possibleNames
     */
    public function resolveTemplateIdentifier(string|array $possibleNames): ?string;

    /**
     * @param string|array<string> $possibleNames
     */
    public function resolvePartialIdentifier(string|array $possibleNames): ?string;

    /**
     * @param string|array<string> $possibleNames
     */
    public function getTemplate(string|array $possibleNames): ?string;

    /**
     * @param string|array<string> $possibleNames
     */
    public function getPartial(string|array $possibleNames): ?string;

    public function setErrorMessageTemplateName(string $errorMessageTemplateName): void;

    public function getErrorMessageTemplateName(): string;
}
