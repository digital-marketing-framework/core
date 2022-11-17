<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver;

enum ConfigurationBehaviour
{
    /**
     * config is used as is
     */
    case Default;
    
    /**
     * the configuration will be treated as an empty array if the passed config is a scalar value
     * this is useful for configurations like:
     * feature = 1
     * ... which also can (but does not have to) have a configuration like:
     * feature.option = foobar
     */
    case IgnoreScalar;
    
    /**
     * the configuration will be used to resolve content
     * which is then cast to an array afterwards
     * - multi-values will be cast to an array
     * - scalar values will be converted (explode)
     * this is useful for configurations like:
     * feature = a,b,c
     * ... which can also be expressed like:
     * feature.list {
     *     1 = a
     *     2 = b
     *     3 = c
     * }
     * ... or even with more complex logic like:
     * feature.list {
     *     1 = a
     *     2.field = field_b
     *     3.if {
     *         foo = bar
     *         then = c
     *         else = c2
     *     }
     * }
     */
    case ResolveContentThenCastToArray;
    
    /**
     * the configuration will be converted to an array if it is a scalar value
     * while the original configuration will be set to the 'self' key within this array
     * which results in the two expressions to be identical:
     * feature = foo
     * feature {
     *     self = foo
     * }
     */
    case ConvertScalarToArrayWithSelfValue;
}
