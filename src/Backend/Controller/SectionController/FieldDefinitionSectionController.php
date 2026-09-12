<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\ConfigurationDocument\ConfigurationDocumentManagerInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareTrait;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\FieldDefinition\FieldContextLabel;
use DigitalMarketingFramework\Core\FieldDefinition\FieldDefinitionManagerInterface;
use DigitalMarketingFramework\Core\FieldDefinition\Storage\FieldDefinitionStorageInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\CoreSettings;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\FieldDefinition\FieldContextInformation;
use DigitalMarketingFramework\Core\Model\FieldDefinition\FieldContextSource;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\FieldDefinition\FieldListDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

/**
 * The field definitions view of the integrations section: lists field contexts and edits the
 * definitions stored for them.
 *
 * The list is over contexts rather than over files: a context that only exists because a plugin
 * declares it appears too, so that it can be found and corrected. One file per folder and
 * context is edited at a time, showing only what that file declares.
 *
 * @extends ListSectionController<FieldContextInformation>
 */
class FieldDefinitionSectionController extends ListSectionController implements GlobalConfigurationAwareInterface, ConfigurationDocumentParserAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use ConfigurationDocumentParserAwareTrait;

    /**
     * A context identifier becomes part of a file name and is referenced from schemas, so it
     * is kept to what is safe and stable in both: no path segments, no spaces.
     */
    public const CONTEXT_IDENTIFIER_PATTERN = '/^[A-Za-z0-9][A-Za-z0-9._-]*$/';

    protected FieldDefinitionStorageInterface $storage;

    protected FieldDefinitionManagerInterface $manager;

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct(
            $keyword,
            $registry,
            'integrations',
            [
                'field-definitions',
                'field-definitions-edit',
                'field-definitions-save',
                'field-definitions-create',
                'field-definitions-delete',
            ]
        );

        $this->storage = $registry->getFieldDefinitionStorage();
        $this->manager = $registry->getFieldDefinitionManager();
    }

    protected function getSchemaDocument(): SchemaDocument
    {
        return $this->registry->getRegistryCollection()->getConfigurationSchemaDocument();
    }

    /**
     * The context an edit belongs to. The editor form posts it under the name the shared
     * configuration editor partial uses for every document it edits.
     */
    protected function getDocumentIdentifier(): string
    {
        return trim($this->getParameters()['documentIdentifier'] ?? '');
    }

    /**
     * The context to create: a freely typed identifier wins over the one picked from the list
     * of contexts that have no definitions yet, since typing one is the deliberate act.
     */
    protected function getNewContextIdentifier(): string
    {
        $parameters = $this->getParameters();
        $contextIdentifier = trim($parameters['newContextIdentifier'] ?? '');

        return $contextIdentifier === '' ? trim($parameters['contextIdentifier'] ?? '') : $contextIdentifier;
    }

    protected function getDocument(): string
    {
        return $this->getParameters()['document'] ?? '';
    }

    /**
     * The folder the request explicitly asks for, or null to let the controller choose.
     */
    protected function getFolder(): ?string
    {
        $folder = $this->getParameters()['folder'] ?? '';

        return $folder === '' ? null : $folder;
    }

    /**
     * @return array<string>
     */
    protected function getContextIdentifiers(): array
    {
        // Stored contexts are folded into the schema document during initialisation, so its
        // contexts are already the union of what code declares and what is stored.
        $identifiers = array_keys($this->getSchemaDocument()->getFieldContexts());

        // Ordered so that contexts sharing an integration sit together. Those belonging to none
        // come last, since they group with nothing and would otherwise break up the runs.
        usort($identifiers, static function (string $a, string $b): int {
            $partsA = FieldContextLabel::parse($a);
            $partsB = FieldContextLabel::parse($b);

            return [$partsA['integration'] === null, $partsA['integration'], $partsA['type'], $partsA['route']]
                <=> [$partsB['integration'] === null, $partsB['integration'], $partsB['type'], $partsB['route']];
        });

        return $identifiers;
    }

    protected function getInformation(string $contextIdentifier, bool $firstOfGroup = true): FieldContextInformation
    {
        $context = $this->getSchemaDocument()->getFieldContext($contextIdentifier);
        $parts = FieldContextLabel::parse($contextIdentifier);

        // The route's own label where it registered one, "Web-To-Lead" rather than "salesforce".
        $route = ($context instanceof FieldListDefinition ? $context->getLabel() : null) ?? $parts['route'] ?? '';

        $integration = $this->getIntegrationLabel($contextIdentifier) ?? $parts['integration'];

        return new FieldContextInformation(
            id: $contextIdentifier,
            label: static::composeLabel($integration, $parts['type'], $route),
            sources: $this->getSources($contextIdentifier),
            fieldCount: $context instanceof FieldListDefinition ? count($context->getFields()) : 0,
            integration: $integration,
            type: $parts['type'],
            route: $route,
            firstOfGroup: $firstOfGroup,
        );
    }

    /**
     * The three parts as one line: "SalesForce Distributor: Web-To-Lead", or "Custom: Test In
     * Foo" where there is no integration to name.
     *
     * Not FieldContextLabel::get(), which has only the identifier and so cannot know a route's
     * label — it would title the editor "Distributor: Salesforce" while the list calls the same
     * record "Web-To-Lead".
     */
    protected static function composeLabel(?string $integration, string $type, string $route): string
    {
        $qualifier = $integration === null ? $type : $integration . ' ' . $type;

        return $route === '' ? $qualifier : $qualifier . ': ' . $route;
    }

    /**
     * The label the integration registered for itself, or null when it registered none.
     *
     * The identifier segment cannot stand in for it: "activecampaign" prettifies to
     * "Activecampaign" where the integration calls itself "ActiveCampaign".
     */
    protected function getIntegrationLabel(string $contextIdentifier): ?string
    {
        $name = FieldContextLabel::parseIntegrationName($contextIdentifier);
        if ($name === null) {
            return null;
        }

        $integrations = $this->getSchemaDocument()
            ->getMainSchema()
            ->getProperty(ConfigurationInterface::KEY_INTEGRATIONS)
            ?->getSchema();

        if (!$integrations instanceof ContainerSchema) {
            return null;
        }

        return $integrations->getProperty($name)?->getSchema()->getRenderingDefinition()->getLabel();
    }

    /**
     * Every source of fields for this context, lowest precedence first.
     *
     * The configured storage folder is always listed, with or without a file, because it is
     * where an override belongs and offering to create one there is the point. Other folders
     * appear only when they actually hold a file — on a system that allows writing to package
     * paths, "every writable folder" is every installed package.
     *
     * @return array<FieldContextSource>
     */
    protected function getSources(string $contextIdentifier): array
    {
        $storageFolder = $this->storage->getStorageFolder();
        $writableFolders = $this->storage->getWritableFolders();

        $sources = [];

        $codeDeclaredFieldCount = $this->manager->getCodeDeclaredFieldCount($contextIdentifier);
        if ($codeDeclaredFieldCount > 0) {
            $sources[] = new FieldContextSource(null, $codeDeclaredFieldCount, false, false);
        }

        foreach ($this->storage->getFolders($contextIdentifier) as $folder) {
            if ($folder === $storageFolder) {
                continue;
            }

            $sources[] = new FieldContextSource(
                $folder,
                $this->getStoredFieldCount($contextIdentifier, $folder),
                true,
                in_array($folder, $writableFolders, true)
            );
        }

        if ($storageFolder !== '') {
            $stored = $this->storage->exists($contextIdentifier, $storageFolder);
            $sources[] = new FieldContextSource(
                $storageFolder,
                $stored ? $this->getStoredFieldCount($contextIdentifier, $storageFolder) : 0,
                $stored,
                in_array($storageFolder, $writableFolders, true)
            );
        }

        return $sources;
    }

    protected function getStoredFieldCount(string $contextIdentifier, string $folder): int
    {
        $content = $this->storage->getContent($contextIdentifier, $folder);

        return $content === null ? 0 : count($this->manager->parse($contextIdentifier, $content)->getFields());
    }

    protected function fetchFilteredCount(array $filters): int
    {
        // TODO no filtering yet
        return count($this->getContextIdentifiers());
    }

    protected function fetchFiltered(array $filters, array $navigation): array
    {
        // TODO no pagination or filtering yet
        $identifiers = $this->getContextIdentifiers();

        $list = [];
        $previousIntegration = null;
        foreach ($identifiers as $identifier) {
            $integration = FieldContextLabel::parse($identifier)['integration'] ?? '';

            // Only a named integration groups. A context belonging to none stands alone.
            $firstOfGroup = $integration === '' || $integration !== $previousIntegration;

            $list[] = $this->getInformation($identifier, $firstOfGroup);
            $previousIntegration = $integration;
        }

        return $list;
    }

    protected function fieldDefinitionsAction(): Response
    {
        // A context that is already listed is created by opening and saving it, so the form
        // only has to cover identifiers nothing knows about yet.
        $this->viewData['storageAvailable'] = $this->storage->getWritableFolders() !== [];

        return parent::listAction();
    }

    /**
     * The folder an action was asked to work on. Actions name it: the list offers one action
     * per source, so there is nothing to infer and nothing that could quietly resolve to a
     * different file on a system configured differently.
     */
    protected function getRequiredFolder(): string
    {
        $folder = $this->getFolder();
        if ($folder === null) {
            throw new DigitalMarketingFrameworkException('No field definition folder given.');
        }

        return $folder;
    }

    protected function isValidContextIdentifier(string $contextIdentifier): bool
    {
        return preg_match(static::CONTEXT_IDENTIFIER_PATTERN, $contextIdentifier) === 1;
    }

    protected function fieldDefinitionsEditAction(): Response
    {
        $this->assignCurrentRouteData(defaultReturnRoute: 'page.integrations.field-definitions');
        $this->addConfigurationEditorAssets();

        $contextIdentifier = (string)$this->getIdentifier();
        $folder = $this->getRequiredFolder();

        $information = $this->getInformation($contextIdentifier);
        $information->setContent($this->withDocumentName(
            $this->storage->getContent($contextIdentifier, $folder) ?? '',
            $information->getLabel()
        ));

        $this->viewData['context'] = $information;
        $this->viewData['folder'] = $folder;
        $this->viewData['readonly'] = $this->storage->isReadOnly($contextIdentifier, $folder);
        $this->viewData['debug'] = $this->globalConfiguration->getGlobalSettings(CoreSettings::class)->debug();

        return $this->render();
    }

    /**
     * Names the document for the editor, which titles what is open by its metadata.
     *
     * The name is not stored: it says nothing the file name does not already say, and these
     * files are shipped in packages and edited by hand, so they are kept free of editor
     * bookkeeping. saveAction() takes it back out again.
     */
    protected function withDocumentName(string $content, string $name): string
    {
        $document = $this->configurationDocumentParser->parseDocument($content);
        $metaData = [
            ConfigurationDocumentManagerInterface::KEY_META_DATA => [
                ConfigurationDocumentManagerInterface::KEY_DOCUMENT_NAME => $name,
            ],
        ];

        return $this->configurationDocumentParser->produceDocument($metaData + $document);
    }

    protected function fieldDefinitionsSaveAction(): Response
    {
        $contextIdentifier = $this->getDocumentIdentifier();
        $folder = $this->getRequiredFolder();

        // setContent() writes the file whether or not the context was already there, so save
        // is a creating action too and is held to the same identifier rule as create.
        if (!$this->isValidContextIdentifier($contextIdentifier)) {
            throw new DigitalMarketingFrameworkException(sprintf('"%s" is not a valid field context identifier.', $contextIdentifier));
        }

        $document = $this->configurationDocumentParser->parseDocument($this->getDocument());
        unset($document[ConfigurationDocumentManagerInterface::KEY_META_DATA]);

        $this->storage->setContent(
            $contextIdentifier,
            $this->configurationDocumentParser->produceDocument($document, $this->manager->getSchemaDocument()),
            $folder
        );

        return $this->redirect('page.integrations.field-definitions-edit', [
            'id' => $contextIdentifier,
            'folder' => $folder,
            'returnUrl' => $this->getReturnUrl(),
        ]);
    }

    protected function fieldDefinitionsCreateAction(): Response
    {
        $contextIdentifier = $this->getNewContextIdentifier();
        if ($contextIdentifier === '') {
            return $this->redirect('page.integrations.field-definitions', ['returnUrl' => $this->getReturnUrl()]);
        }

        $folder = $this->getFolder() ?? $this->storage->getStorageFolder();

        if (!$this->isValidContextIdentifier($contextIdentifier)) {
            return $this->refuseCreation(sprintf('"%s" is not a valid field context identifier.', $contextIdentifier));
        }

        // A row offers creation only for the folder that has no file yet, so reaching this with
        // one that does means the request is stale or was typed into the create box. Opening the
        // existing file instead would be a create action that quietly creates nothing.
        if ($this->storage->exists($contextIdentifier, $folder)) {
            return $this->refuseCreation(
                sprintf('A field definition file for "%s" already exists in "%s".', $contextIdentifier, $folder),
                $contextIdentifier,
                $folder
            );
        }

        $this->storage->setContent(
            $contextIdentifier,
            $this->configurationDocumentParser->produceDocument(
                [FieldDefinitionManagerInterface::KEY_FIELDS => []],
                $this->manager->getSchemaDocument()
            ),
            $folder
        );

        return $this->redirect('page.integrations.field-definitions-edit', [
            'id' => $contextIdentifier,
            'folder' => $folder,
            'returnUrl' => $this->getReturnUrl(),
        ]);
    }

    /**
     * Renders the create action rather than creating anything.
     *
     * A refusal here has somewhere useful to send the reader — the file that already exists —
     * which the generic error page cannot know about. That is what an action template that is
     * only ever seen when something goes wrong buys.
     */
    protected function refuseCreation(string $message, string $contextIdentifier = '', string $folder = ''): Response
    {
        $this->viewData['error'] = $message;
        $this->viewData['contextIdentifier'] = $contextIdentifier;
        $this->viewData['folder'] = $folder;
        $this->viewData['readonly'] = $contextIdentifier !== '' && $this->storage->isReadOnly($contextIdentifier, $folder);
        $this->viewData['returnUrl'] = $this->getReturnUrl($this->uriBuilder->build('page.integrations.field-definitions'));

        return $this->render();
    }

    protected function fieldDefinitionsDeleteAction(): Response
    {
        $folder = $this->getRequiredFolder();
        foreach ($this->getSelectedItems() as $contextIdentifier) {
            $this->storage->delete((string)$contextIdentifier, $folder);
        }

        return $this->redirect('page.integrations.field-definitions', ['returnUrl' => $this->getReturnUrl()]);
    }
}
