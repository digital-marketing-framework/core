<?php

namespace DigitalMarketingFramework\Core\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\IdentifierCollector\IdentifierCollectorInterface;
use DigitalMarketingFramework\Core\Notification\NotificationChannelInterface;
use DigitalMarketingFramework\Core\Plugin\ConfigurablePluginInterface;
use DigitalMarketingFramework\Core\Plugin\IntegrationPluginInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

/**
 * What this installation does with data, by integration and by plugin type.
 *
 * Two shapes, because the code has two shapes. Routes and identifier collectors carry an
 * IntegrationInfo, which exists to group several of them under one system — Salesforce has two
 * outbound routes, Pardot a route and an identification. Data privacy plugins and notification
 * channels carry none, because there is nothing to group: the plugin is the integration. So the
 * first are listed under their integration and the second under their kind, which is in both
 * cases the axis that actually has more than one thing on it.
 */
class IntegrationOverviewSectionController extends SectionController
{
    /**
     * Kinds whose interface name does not read well once prettified.
     */
    protected const KIND_LABELS = [
        IdentifierCollectorInterface::class => 'Identification',
    ];

    public function __construct(string $keyword, RegistryInterface $registry)
    {
        parent::__construct($keyword, $registry, 'integrations', ['overview']);
    }

    protected function overviewAction(): Response
    {
        $this->viewData['integrations'] = $this->getIntegrations();
        $this->viewData['pluginTypes'] = $this->getPluginTypes();

        return $this->render();
    }

    /**
     * Everything registered for an interface that carries an integration, grouped by it.
     *
     * The interfaces are asked for rather than named: `OutboundRouteInterface` lives in
     * distributor-core and cannot be referenced from here, so the registries are asked what they
     * hold and the answers are filtered by what they implement. A plugin type added by any
     * package appears here without this controller knowing about it.
     *
     * @return array<string,array{label:string,contributions:array<array{kind:string,label:string}>}>
     */
    protected function getIntegrations(): array
    {
        $collection = $this->registry->getRegistryCollection();

        $integrations = [];
        foreach ($collection->getAllPluginInterfaces() as $interface) {
            if (!is_a($interface, IntegrationPluginInterface::class, true)) {
                continue;
            }

            $kind = $this->getKindLabel($interface);
            foreach ($collection->getAllPluginClasses($interface) as $keyword => $class) {
                $integrationInfo = $class::getDefaultIntegrationInfo();
                $name = $integrationInfo->getName();

                $integrations[$name]['label'] ??= $integrationInfo->getLabel() ?? GeneralUtility::getLabelFromValue($name);
                $integrations[$name]['contributions'][] = [
                    'kind' => $kind,
                    'label' => $this->getPluginLabel($class, $keyword),
                ];
            }
        }

        foreach ($integrations as &$integration) {
            usort($integration['contributions'], static fn (array $a, array $b): int => [$a['kind'], $a['label']] <=> [$b['kind'], $b['label']]);
        }

        ksort($integrations);

        return $integrations;
    }

    /**
     * The kinds that are listed by type rather than by integration.
     *
     * Both interfaces belong to core, so unlike the integrations above they can simply be named.
     *
     * @return array<array{label:string,plugins:array<string>}>
     */
    protected function getPluginTypes(): array
    {
        $dataPrivacyPlugins = [];
        foreach ($this->registry->getDataPrivacyManager()->getPlugins() as $keyword => $plugin) {
            $dataPrivacyPlugins[] = $plugin->getLabel() !== '' ? $plugin->getLabel() : GeneralUtility::getLabelFromValue($keyword);
        }

        $notificationChannels = [];
        foreach ($this->registry->getAllPluginClasses(NotificationChannelInterface::class) as $keyword => $class) {
            $notificationChannels[] = $this->getPluginLabel($class, $keyword);
        }

        sort($dataPrivacyPlugins);
        sort($notificationChannels);

        // An empty kind is left out rather than shown as an empty heading: a small installation
        // should produce a short page, not a page of absences.
        // Headings over a list, so they are named here rather than derived from the interface:
        // "Notification Channels" reads as a heading, "Notification Channel" does not.
        return array_values(array_filter([
            ['label' => 'Data Privacy', 'plugins' => $dataPrivacyPlugins],
            ['label' => 'Notification Channels', 'plugins' => $notificationChannels],
        ], static fn (array $type): bool => $type['plugins'] !== []));
    }

    /**
     * @param class-string $interface
     */
    protected function getKindLabel(string $interface): string
    {
        if (isset(static::KIND_LABELS[$interface])) {
            return static::KIND_LABELS[$interface];
        }

        $name = substr((string)strrchr('\\' . $interface, '\\'), 1);

        return GeneralUtility::getLabelFromValue(substr($name, 0, -strlen('Interface')));
    }

    /**
     * @param class-string $class
     */
    protected function getPluginLabel(string $class, string $keyword): string
    {
        $label = is_a($class, ConfigurablePluginInterface::class, true) ? $class::getLabel() : null;

        return $label ?? GeneralUtility::getLabelFromValue($keyword);
    }
}
