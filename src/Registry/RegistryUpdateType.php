<?php

namespace DigitalMarketingFramework\Core\Registry;

enum RegistryUpdateType
{
    /**
     * The first update that is called from outside always needs to be about the global configuration.
     *
     * Global configruation is not coming from configuration documents. It is system-wide configuration that is added from outside.
     * By default the global configuration is empty. It is up to the surrounding system to change that.
     */
    case GLOBAL_CONFIGURATION;

    /**
     * The second update that is called from outside always needs to be about services.
     *
     * Services are singleton classes which server a specific purpose that is needed in multiple locations.
     * They may rely on global configuration to be present, which why they come second.
     * There are default implementations for all services, though some may be dummy implementations without any actual functionality.
     */
    case SERVICE;

    /**
     * The third update that is called from the outside is about plugins.
     *
     * Every plugin implements a specific interface which defines the type of the plugin.
     * Plugins are not singletons and they are created on demand. Which implementation is used depends on the given keyword from the requesting party.
     * Plugins may need global configuration and services, which is why they are registered last.
     */
    case PLUGIN;
}
