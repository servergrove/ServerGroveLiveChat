<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Description of CacheRepository
 *
 */
class CacheRepository extends DocumentRepository
{
    public function getByKey($key)
    {
       return $this->findOneBy(array('key' => $key));
    }
}