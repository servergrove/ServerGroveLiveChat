<?php

namespace ServerGrove\SGLiveChatBundle\Tests;

use Symfony\Bundle\DoctrineMongoDBBundle\DependencyInjection\DoctrineMongoDBExtension;

use ServerGrove\SGLiveChatBundle\DependencyInjection\SGLiveChatExtension;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{

    private $kernel, $cacheEngineName;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->kernel = new TestKernel('test', false);
        $this->kernel->boot();
    }

    protected function preSetup()
    {
        $this->setCacheEngineName('mongo');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->preSetup();
        parent::setUp();
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
