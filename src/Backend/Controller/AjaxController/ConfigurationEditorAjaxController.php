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
        protected string $documentType,
    ) {
        parent::__construct(
            $keyword,
            $registry,
            'configuration-editor',
            ['schema', 'defaults', 'merge', 'split', 'update-includes']
        );
    }

    abstract protected function getSchemaDocument(): SchemaDocument;

    protected function getDocumentType(?Request $request = null): string
    {
        if (!$request instanceof Request) {
            $request = $this->request;
        }

        return $request->getArguments()['documentType'] ?? '';
    }

    public function matchRequest(Request $request): bool
    {
        if ($this->getDocumentType($request) !== $this->documentType) {
            return false;
        }

        return parent::matchRequest($request);
    }

    /**
     * @return array<string,mixed>
     */
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

    /**
     * @return array<string,mixed>
     */
    public function parseDocument(string $document): array
    {
        return $this->configurationDocumentParser->parseDocument($document);
    }

    /**
     * @param array<string,mixed> $configuration
     */
    public function produceDocument(array $configuration): string
    {
        return $this->configurationDocumentParser->produceDocument($configuration, $this->getSchemaDocument());
    }

    protected function schemaAction(): Response
    {
        $data = $this->getSchemaDocument()->toArray();

        return new JsonResponse($data);
    }

    protected function defaultsAction(): Response
    {
        $defaults = $this->getDefaultConfiguration();
        $this->preSaveDataTransform($defaults);

        return new JsonResponse($defaults);
    }

    /**
     * @param array<string,mixed> $configuration
     *
     * @return array<string,mixed>
     */
    abstract protected function mergeConfiguration(array $configuration, bool $inheritedConfigurationOnly = false): array;

    /**
     * @param array<string,mixed> $mergedConfiguration
     *
     * @return array<string,mixed>
     */
    abstract protected function splitConfiguration(array $mergedConfiguration): array;

    /**
     * @param array<string,mixed> $referenceMergedConfiguration
     * @param array<string,mixed> $mergedConfiguration
     *
     * @return array<string,mixed>
     */
    abstract protected function processIncludesChange(array $referenceMergedConfiguration, array $mergedConfiguration, bool $inheritedConfigurationOnly = false): array;

    protected function mergeAction(): Response
    {
        $document = $this->request->getData()['document'] ?? '';
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

    protected function splitAction(): Response
    {
        $mergedConfiguration = $this->request->getData();
        $splitConfiguration = $this->splitConfiguration($mergedConfiguration);
        $splitDocument = $this->produceDocument($splitConfiguration);

        return new JsonResponse(['document' => $splitDocument]);
    }

    protected function updateIncludesAction(): Response
    {
        $data = $this->request->getData();

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
