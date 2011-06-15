<?php

namespace ServerGrove\SGLiveChatBundle\Cache;

use ServerGrove\SGLiveChatBundle\Cache\Engine\Apc;

/**
 * Description of Manager
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class Manager implements Cacheable
{

    /**
     * @var ServerGrove\SGLiveChatBundle\Cache\Engine\Base
     */
    private $engine;

    /**
     * @return ServerGrove\SGLiveChatBundle\Cache\Engine\Base
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Constructor
     * @param Engine\Base $engine
     */
    public function __construct(Engine\Base $engine)
    {
        $this->engine = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $var, $ttl = self::DEFAULT_TTL)
    {
        return $this->getEngine()->set($key, $var, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->getEngine()->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->getEngine()->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return $this->getEngine()->remove($key);
    }
}