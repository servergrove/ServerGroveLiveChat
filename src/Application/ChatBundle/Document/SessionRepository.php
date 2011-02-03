<?php

namespace Application\ChatBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;
use MongoDate;

/**
 * Description of SessionRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class SessionRepository extends DocumentRepository
{

    public function getRequestedChats()
    {
        return $this->createQueryBuilder()->field('updatedAt')->range(new MongoDate(time() - 300), new MongoDate(time()))->getQuery()->execute();
    }

    public function closeSessions()
    {
        $this->createQueryBuilder()->field('statusId')->set(Session::STATUS_CANCELED)->field('statusId')->notIn(array(
            Session::STATUS_CANCELED,
            Session::STATUS_CLOSED))->field('updatedAt')->lt(new MongoDate(time() - 300))->update()->getQuery()->execute();
    }

    public function getSessionIfNotFinished($id)
    {
        return $this->createQueryBuilder()->field('id')->equals($id)->field('statusId')->notIn(array(
            Session::STATUS_CANCELED,
            Session::STATUS_CLOSED))->field('updatedAt')->gt(new MongoDate(time() - 300))->getQuery()->getSingleResult();
    }

    public function getOpenInvites()
    {
        return $this->find(array(
            'statusId' => Session::STATUS_INVITE));
    }

}