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
        return $this->getDocumentManager()->createQueryBuilder('ChatBundle:Session')->field('updatedAt')->range(new MongoDate(time() - 300), new MongoDate(time()))->getQuery()->execute();
    }

    public function closeSessions()
    {
        $this->getDocumentManager()->createQueryBuilder('ChatBundle:Session')
                ->field('statusId')->set(Session::STATUS_CANCELED)
                ->field('statusId')->notIn(array(Session::STATUS_CANCELED, Session::STATUS_CLOSED))
                ->field('updatedAt')->lt(new MongoDate(time() - 300))->getQuery()->execute();
    }

    public function getSessionIfNotFinished($id)
    {
        return $this->getDocumentManager()->createQueryBuilder('ChatBundle:Session')
                ->field('id')->equals($id)
                ->field('statusId')->notIn(array(Session::STATUS_CANCELED, Session::STATUS_CLOSED))
                ->field('updatedAt')->gt(new MongoDate(time() - 300))->getQuery()->getSingleResult();
    }

}