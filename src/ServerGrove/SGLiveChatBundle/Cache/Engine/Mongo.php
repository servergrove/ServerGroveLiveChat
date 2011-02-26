<?php

namespace ServerGrove\SGLiveChatBundle\Cache\Engine;

use ServerGrove\SGLiveChatBundle\Document\CacheEntry;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Description of Apc
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class Mongo extends Base
{

    /**
     *
     * @var DocumentManager
     */
    protected $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function set($key, $var, $ttl = self::DEFAULT_TTL)
    {
        $cache = $this->dm->getRepository('SGLiveChatBundle:CacheEntry')->getByKey($key);

        if (!$cache) {
            $cache = new CacheEntry();
            $cache->setKey($key);
            $this->dm->persist($cache);
        }
        $cache->setTtl($ttl);
        $cache->setData($var);

        $this->dm->flush();

        return true;
    }

    public function get($key, $default = null)
    {
        $cache = $this->dm->getRepository('SGLiveChatBundle:CacheEntry')->getByKey($key);
        if ($cache && !$cache->isExpired()) {
            return $cache->getData();
        }

        return $default;
    }

    public function has($key)
    {
        $cache = $this->dm->getRepository('SGLiveChatBundle:CacheEntry')->getByKey($key);
        return ($cache !== null && !$cache->isExpired());
    }

    public function remove($key)
    {
        $cache = $this->dm->getRepository('SGLiveChatBundle:CacheEntry')->getByKey($key);
        if ($cache) {
            $this->dm->remove($cache);
        }
        $this->dm->flush();

        return true;
    }

}