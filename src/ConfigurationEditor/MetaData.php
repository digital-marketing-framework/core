<?php

namespace DigitalMarketingFramework\Core\ConfigurationEditor;

interface MetaData
{
    public const DEFAULT_DOCUMENT_TYPE = 'configuration-document';

    public const SCRIPTS = [
        'js-main' => 'PKG:digital-marketing-framework/core/res/assets/config-editor/scripts/index.js',
    ];

    public const STYLES = [
        'css-main' => 'PKG:digital-marketing-framework/core/res/assets/config-editor/styles/index.css',
        'css-fonts' => 'PKG:digital-marketing-framework/core/res/assets/config-editor/styles/type.css',
    ];

    public const ASSETS = [
        'PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-Bold.ttf',
        'PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-Medium.ttf',
        'PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-Regular.ttf',
        'PKG:digital-marketing-framework/core/res/assets/config-editor/fonts/caveat/Caveat-SemiBold.ttf',
    ];
}
