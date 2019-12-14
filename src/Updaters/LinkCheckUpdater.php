<?php

namespace KirchenImWeb\Updaters;

use KirchenImWeb\Helpers\Database;

/**
 * Checks websites for broken links.
 *
 * @package KirchenImWeb\Updaters
 */
class LinkCheckUpdater
{
    public function check(): void
    {
        $websites = Database::getInstance()->getWebsitesToCheck(10);
        foreach ($websites as $website) {
            $l = new LinkCheck($website['url']);
            Database::getInstance()->updateWebsiteCheck(
                $website['websiteId'],
                $l->getStatusCode(),
                $l->getRedirectTarget()
            );
        }
    }
}
