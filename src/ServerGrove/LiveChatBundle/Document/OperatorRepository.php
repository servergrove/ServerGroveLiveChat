<?php

namespace ServerGrove\LiveChatBundle\Document;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use ServerGrove\LiveChatBundle\Document\Operator;
use MongoDate;

/**
 * Description of OperatorRepository
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
            ->field('updatedAt')->lt(new MongoDate(time() - 86400))
            ->update()->getQuery()
            ->execute();
    }
}