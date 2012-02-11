<?php

namespace ServerGrove\LiveChatBundle\Document;

/**
 * Class OperatorRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorRepository extends DocumentRepository
{

    public function getOnlineOperatorsCount()
    {
        return $this->createQueryBuilder()->field('isOnline')->equals(true)->getQuery()->count();
    }

    public function closeOldLogins()
    {
        $this->createQueryBuilder()
            ->field('isOnline')->set(false)
            ->field('isOnline')->equals(true)
            ->field('updatedAt')->lt(new \MongoDate(time() - 86400))
            ->update()->getQuery()
            ->execute();
    }
}