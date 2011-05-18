<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Description of CannedMessageRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class CannedMessageRepository extends DocumentRepository
{

    public function findSlice($offset, $length)
    {
        return $this->createQueryBuilder()->skip($offset)->limit($length)->getQuery()->execute();
    }

}