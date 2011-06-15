<?php

namespace ServerGrove\SGLiveChatBundle\Tests\Cache;
use ServerGrove\SGLiveChatBundle\Tests\TestCase;

/**
 * Manager test case.
 */
abstract class ManagerTestAbstract extends TestCase
{

    /**
     * @var Manager
     */
    private $Manager;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->Manager = $this->createManager();
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Cache\Manager
     */
    protected abstract function createManager();

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Manager = null;
        parent::tearDown();
    }

    /**
     * Tests Manager->__construct()
     */
    public function testCorrectInstance()
    {
        $this->assertEquals('ServerGrove\\SGLiveChatBundle\\Cache\\Manager', get_class($this->Manager));
    }

    /**
     * Tests Manager->set()
     */
    public function testSet()
    {
        $className = get_class($this);
        $this->Manager->set(__NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar', $className);
        $this->assertEquals($className, $this->Manager->get(__NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar'), $this->Manager->get(__NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar'));
    }

    /**
     * Tests Manager->get()
     */
    public function testGet()
    {
        $this->assertEquals(__NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar', $this->Manager->get(md5(microtime()), __NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar'));
    }

    /**
     * Tests Manager->has()
     */
    public function testHas()
    {
        $this->assertFalse($this->Manager->has(md5(microtime())));
        $this->Manager->set(__NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar', true);
        $this->assertTrue($this->Manager->has(__NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar'));
    }

    /**
     * Tests Manager->remove()
     */
    public function testRemove()
    {
        $this->Manager->set(__NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar', true);
        $this->assertTrue($this->Manager->has(__NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar'));
        $this->Manager->remove(__NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar');
        $this->assertFalse($this->Manager->has(__NAMESPACE__ . __CLASS__ . __FUNCTION__ . 'MyVar'));
    }

}

