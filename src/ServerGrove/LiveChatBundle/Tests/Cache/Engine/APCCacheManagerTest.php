<?php

namespace ServerGrove\LiveChatBundle\Tests\Cache;

use ServerGrove\LiveChatBundle\Cache\Manager;
use ServerGrove\LiveChatBundle\Cache\Engine\Apc;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class APCCacheManagerTest extends ManagerTestAbstract
{

    protected function preSetup()
    {
        if (!function_exists('\apc_store')) {
            $this->markTestSkipped('APC not found');
        }

        if (!ini_get('apc.enabled')) {
            $this->markTestSkipped('APC not found');
        }

        $this->setCacheEngineName('apc');
    }

    protected function createManager()
    {
        return new Manager(new Apc());
    }
}