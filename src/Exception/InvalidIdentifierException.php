<?php

namespace DigitalMarketingFramework\Core\Exception;

/**
 * An invalid identifier could indicate an attempt to guess or manipulate a user identifier, either by a human or a bot.
 * A penalty should be considered if this exception is caught to prevent automated bot shenanigans.
 */
class InvalidIdentifierException extends DigitalMarketingFrameworkException
{
}
