<?php

namespace ServerGrove\SGLiveChatBundle\Tests\Cache;

use ServerGrove\SGLiveChatBundle\Cache\Manager;
use ServerGrove\SGLiveChatBundle\Cache\Engine\Apc;

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