<?php

namespace ServerGrove\LiveChatBundle\Tests;

use Symfony\Bundle\DoctrineMongoDBBundle\DependencyInjection\DoctrineMongoDBExtension;
use ServerGrove\LiveChatBundle\DependencyInjection\ServerGroveLiveChatExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class TestCase extends WebTestCase
{

    private $cacheEngineName;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->preSetup();
        parent::setUp();
        self::$kernel = self::createKernel();
        self::$kernel->boot();
    }

    protected function preSetup()
    {
        $this->setCacheEngineName('mongo');
    }

    protected function getContainer()
    {
        return self::$kernel->getContainer();
    }

    protected function setCacheEngineName($name)
    {
        $this->cacheEngineName = $name;
    }

    protected function getCacheEngineName()
    {
        return $this->cacheEngineName;
    }

}
