<?php

namespace ServerGrove\LiveChatBundle\Tests\Cache;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
use ServerGrove\LiveChatBundle\Cache\Engine\Mongo;

use ServerGrove\LiveChatBundle\Cache\Manager;

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