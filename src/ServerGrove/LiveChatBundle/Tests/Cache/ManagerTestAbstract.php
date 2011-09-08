<?php

namespace ServerGrove\LiveChatBundle\Tests\Cache;
use ServerGrove\LiveChatBundle\Tests\TestCase;

/**
 * Manager test case.
 */
abstract class ManagerTestAbstract extends TestCase
{

    /**
     * @var \ServerGrove\LiveChatBundle\Cache\Manager
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
     * @return \ServerGrove\LiveChatBundle\Cache\Manager
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
        $this->assertEquals('ServerGrove\\LiveChatBundle\\Cache\\Manager', get_class($this->Manager));
    }

    /**
     * Tests Manager->set()
     */
    public function testSet()
    {
        $className = get_class($this);
        $this->Manager->set($key = $className . __FUNCTION__ . 'MyVar', $className);
        $this->assertEquals($className, $this->Manager->get($key), $this->Manager->get($key));
    }

    /**
     * Tests Manager->get()
     */
    public function testGet()
    {
        $className = get_class($this);
        $this->assertEquals($className . __FUNCTION__ . 'MyVar', $this->Manager->get(md5(microtime()), $className . __FUNCTION__ . 'MyVar'));
    }

    /**
     * Tests Manager->has()
     */
    public function testHas()
    {
        $className = get_class($this);
        $this->assertFalse($this->Manager->has(md5(microtime())));
        $this->Manager->set($className . __FUNCTION__ . 'MyVar', true);
        $this->assertTrue($this->Manager->has($className . __FUNCTION__ . 'MyVar'));
    }

    /**
     * Tests Manager->remove()
     */
    public function testRemove()
    {
        $className = get_class($this);
        $this->Manager->set($className . __FUNCTION__ . 'MyVar', true);
        $this->assertTrue($this->Manager->has($className . __FUNCTION__ . 'MyVar'));
        $this->Manager->remove($className . __FUNCTION__ . 'MyVar');
        $this->assertFalse($this->Manager->has($className . __FUNCTION__ . 'MyVar'));
    }

}

