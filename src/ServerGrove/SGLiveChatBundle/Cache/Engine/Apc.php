<?php

namespace ServerGrove\SGLiveChatBundle\Cache\Engine;

use ServerGrove\SGLiveChatBundle\Cache\Cacheable;

/**
 * Description of Apc
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class Apc extends Base
{

    public function set($key, $var, $ttl = self::DEFAULT_TTL)
    {
        return apc_store($key, $var, $ttl);
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return apc_fetch($key);
        }

        return $default;
    }

    public function has($key)
    {
        return apc_exists($key);
    }

    public function remove($key)
    {
        return apc_delete($key);
    }

}