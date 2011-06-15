<?php

namespace ServerGrove\SGLiveChatBundle\Tests\Cache;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
use ServerGrove\SGLiveChatBundle\Cache\Engine\Mongo;

use ServerGrove\SGLiveChatBundle\Cache\Manager;

class MongoCacheManagerTest extends ManagerTestAbstract
{

    protected function preSetup()
    {
        if (!class_exists('\Mongo')) {
            $this->markTestSkipped('Mongo not found');
        }

        $this->setCacheEngineName('mongo');
    }

    protected function createManager()
    {
        return new Manager(new Mongo($this->getContainer()->get('doctrine.odm.mongodb.document_manager')));
    }
}