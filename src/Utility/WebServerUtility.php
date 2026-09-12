<?php

namespace DigitalMarketingFramework\Core\Utility;

class WebServerUtility
{
    /**
     * Whether the web server reads per-directory access files.
     *
     * Only Apache does, and writing one anywhere else is worse than writing none: a file that
     * looks like protection and is silently ignored invites someone to stop looking.
     *
     * On CLI there is no server to ask, so the answer is no. A folder created by a command is
     * protected by the next web request instead, since the storages check on registration.
     */
    public static function supportsAccessFile(): bool
    {
        $software = (string)($_SERVER['SERVER_SOFTWARE'] ?? '');

        return $software === 'apache' || str_starts_with($software, 'Apache');
    }
}
