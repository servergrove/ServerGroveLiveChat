<?php

namespace ServerGrove\SGLiveChatBundle\Cache\Engine;

/**
 * Description of Apc
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class Apc extends Base
{

    public function __construct()
    {
        if (!function_exists('apc_store')) {
            throw new \Exception("APC extension not installed.");
        }
    }

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
        if (!function_exists('\apc_exists')) {
            $success = false;
            apc_fetch($key, $success);
            return $success;
        }
        return apc_exists($key);
    }

    public function remove($key)
    {
        return apc_delete($key);
    }

}