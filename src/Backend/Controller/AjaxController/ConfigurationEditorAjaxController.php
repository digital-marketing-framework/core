<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\AjaxController;

use DigitalMarketingFramework\Core\Backend\Request;
use DigitalMarketingFramework\Core\Backend\Response\JsonResponse;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserAwareTrait;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorAwareInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorAwareTrait;

abstract class ConfigurationEditorAjaxController extends AjaxController implements SchemaProcessorAwareInterface, ConfigurationDocumentParserAwareInterface
{
    use SchemaProcessorAwareTrait;
    use ConfigurationDocumentParserAwareTrait;

    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        protected SchemaDocument $schemaDocument,
        protected string $documentType,
    ) {
        parent::__construct(
            $keyword,
            $registry,
            'configuration-editor',
            ['schema', 'defaults', 'merge', 'split', 'update-includes']
        );
    }

    protected function getDocumentType(Request $request): string
    {
        return $request->getArguments()['documentType'] ?? '';
    }

    public function matchRequest(Request $request): bool
    {
        if ($this->getDocumentType($request) !== $this->documentType) {
            return false;
        }

        return parent::matchRequest($request);
    }

    public function getSchemaDocument(): SchemaDocument
    {
        return $this->schemaDocument;
    }

    public function getDefaultConfiguration(): array
    {
        return $this->schemaProcessor->getDefaultValue($this->getSchemaDocument());
    }

    public function preSaveDataTransform(mixed &$data): void
    {
        $this->schemaProcessor->preSaveDataTransform($this->getSchemaDocument(), $data);
    }

    public function convertValueTypes(mixed &$data): void
    {
        $this->schemaProcessor->convertValueTypes($this->getSchemaDocument(), $data);
    }

    public function parseDocument(string $document): array
    {
        return $this->configurationDocumentParser->parseDocument($document);
    }

    public function produceDocument(array $configuration): string
    {
        return $this->configurationDocumentParser->produceDocument($configuration, $this->getSchemaDocument());
    }

    protected function schemaAction(Request $request): Response
    {
        $data = $this->getSchemaDocument()->toArray();

        return new JsonResponse($data);
    }

    protected function defaultsAction(Request $request): Response
    {
        $defaults = $this->getDefaultConfiguration();
        $this->preSaveDataTransform($defaults);

        return new JsonResponse($defaults);
    }

    abstract protected function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array;

    abstract protected function splitConfiguration(array $mergedConfiguration): array;

    abstract protected function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array;

    protected function mergeAction(Request $request): Response
    {
        $document = $request->getData()['document'] ?? '';
        $configuration = $this->parseDocument($document);

        $mergedConfiguration = $this->mergeConfiguration($configuration);
        $mergedInheritedConfiguration = $this->mergeConfiguration($configuration, inheritedConfigurationOnly: true);

        $this->preSaveDataTransform($mergedConfiguration);
        $this->preSaveDataTransform($mergedInheritedConfiguration);

        return new JsonResponse([
            'configuration' => $mergedConfiguration,
            'inheritedConfiguration' => $mergedInheritedConfiguration,
        ]);
    }

    protected function splitAction(Request $request): Response
    {
        $mergedConfiguration = $request->getData();
        $splitConfiguration = $this->splitConfiguration($mergedConfiguration);
        $splitDocument = $this->produceDocument($splitConfiguration);

        return new JsonResponse(['document' => $splitDocument]);
    }

    protected function updateIncludesAction(Request $request): Response
    {
        $data = $request->getData();

        $mergedConfiguration = $this->processIncludesChange(
            $data['referenceData'],
            $data['newData']
        );

        $mergedInheritedConfiguration = $this->processIncludesChange(
            $data['referenceData'],
            $data['newData'],
            inheritedConfigurationOnly: true
        );

        $this->preSaveDataTransform($mergedConfiguration);
        $this->preSaveDataTransform($mergedInheritedConfiguration);

        return new JsonResponse([
            'configuration' => $mergedConfiguration,
            'inheritedConfiguration' => $mergedInheritedConfiguration,
        ]);
    }
}
