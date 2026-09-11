<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom;

/**
 * A boolean whose unspecified state means "take the value from somewhere else in this
 * configuration", such as an outbound route falling back to the general route settings.
 *
 * This is not document inheritance, which happens on its own and offers nothing to decide.
 * Where the value comes from instead is the caller's business — convert() only reports that
 * none was given here.
 */
class InheritableBooleanSchema extends NullableBooleanSchema
{
    public const VALUE_INHERIT = 'inherit';

    public const VALUE_NULL = self::VALUE_INHERIT;

    /**
     * Declared here on purpose: PHP resolves self:: in a default parameter value against the
     * class that declares the constructor, so inheriting the parent's would default this
     * schema to the parent's keyword instead of "inherit".
     */
    public function __construct(string $defaultValue = self::VALUE_INHERIT)
    {
        parent::__construct($defaultValue);
    }
}
