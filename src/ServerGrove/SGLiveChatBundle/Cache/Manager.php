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
     * @var ServerGrove\SGLiveChatBundle\Cache\Engine\Apc
     */
    private $engine;

    public function __construct()
    {
        $this->engine = new Apc();
    }

    public function set($key, $var, $ttl = self::DEFAULT_TTL)
    {
        return $this->engine->set($key, $var, $ttl);
    }

    public function get($key, $default = null)
    {
        return $this->engine->get($key, $default);
    }

    public function has($key)
    {
        return $this->engine->has($key);
    }

    public function remove($key) {
        return $this->engine->remove($key);
    }
}