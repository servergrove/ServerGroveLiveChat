<?php

namespace ServerGrove\SGLiveChatBundle\Cache;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
interface Cacheable
{

    /**
     * Value of default time to live for a cached var
     * @var Integer
     */
    const DEFAULT_TTL = 3600;

    /**
     * Stores a variable in the cache engine.
     * Returns TRUE if success, otherwise FALSE.
     *
     * @param string $key An identification for the cached var
     * @param mixed $var The var to be stored
     * @param Integer $ttl Lifetime in seconds that the var must remain in cache
     * @return boolean
     */
    function set($key, $var, $ttl = self::DEFAULT_TTL);

    /**
     * Retrieves the var identified by $key if exists in cache, otherwise $default value
     *
     * @param string $key The identification for the cached var
     * @param mixed $default The returned value if $key doesn't exists in cache
     * @return mixed
     */
    function get($key, $default = null);

    /**
     * Returns TRUE if exists a var identified by $key in the cache engine, otherwise FALSE
     *
     * @param string $key The identification for the cached var
     * @return boolean
     */
    function has($key);

    /**
     * Removes the var identified by $key of the cache engine.
     * Returns TRUE if success, otherwise FALSE.
     *
     * @param string $key The identification for the cached var
     * @return boolean
     */
    function remove($key);
}