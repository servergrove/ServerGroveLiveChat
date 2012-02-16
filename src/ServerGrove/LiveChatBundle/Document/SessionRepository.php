<?php

namespace ServerGrove\LiveChatBundle\Document;

use MongoDate;

/**
 * Description of SessionRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class SessionRepository extends DocumentRepository
{

    public function findSlice($offset, $length)
    {
        return $this->createQueryBuilder()->skip($offset)->limit($length)->sort('createdAt', 'desc')->getQuery()->execute();
    }

    public function getRequestedChats()
    {
        $qb = $this->createQueryBuilder();
        return $qb->addOr(
            $qb->expr()->field('statusId')->equals(Session::STATUS_WAITING)
        )->addOr(
            $qb->expr()->field('updatedAt')->range(new MongoDate(time() - 300), new MongoDate(time()))
        )->sort('createdAt', 'desc')
            ->getQuery()
            ->execute();
    }

    public function getRequestedChatsArray()
    {
        return array_map(function (Session $chat)
        {
            $operator = array();
            if ($chat->getOperator()) {
                $operator['id'] = $chat->getOperator()->getId();
                $operator['name'] = $chat->getOperator()->getName();
            }
            return array(
                'id'         => $chat->getId(),
                'visitor'    => array(
                    'id'    => $chat->getVisitor()->getId(),
                    'name'  => $chat->getVisitor()->getName(),
                    'email' => $chat->getVisitor()->getEmail()
                ),
                'question'   => $chat->getQuestion(),
                'createdAt'  => $chat->getCreatedAt()->format('Y-m-d H:i:s'),
                'duration'   => $chat->getUpdatedAt()->format('U') - $chat->getCreatedAt()->format('U'),
                'operator'   => $operator,
                'status'     => $chat->getStatus(),
                'rating'     => array(
                    'grade'    => $chat->getRating()->getGrade(),
                    'comments' => $chat->getRating()->getComments()
                ),
                'closed'     => in_array($chat->getStatusId(), array(Session::STATUS_CLOSED, Session::STATUS_CANCELED)),
                'inProgress' => Session::STATUS_IN_PROGRESS == $chat->getStatusId(),
                'acceptable' => Session::STATUS_WAITING == $chat->getStatusId()

            );
        }, array_values($this->getRequestedChats()->toArray()));
    }

    public function closeSessions()
    {
        $status = array(
            Session::STATUS_CANCELED,
            Session::STATUS_WAITING,
            Session::STATUS_CLOSED
        );
        $this->createQueryBuilder()->field('statusId')->set(Session::STATUS_CANCELED)->field('statusId')->notIn($status)->field('updatedAt')->lt(new MongoDate(time() - 300))->update()->getQuery()->execute();
    }

    public function getSessionIfNotFinished($id)
    {
        $status = array(
            Session::STATUS_CANCELED,
            Session::STATUS_CLOSED
        );
        return $this->createQueryBuilder()->field('id')->equals($id)->field('statusId')->notIn($status)->getQuery()->getSingleResult();
    }

    public function getOpenInvitesForVisitor(Visitor $visitor)
    {
        return $this->createQueryBuilder()
            ->field('visitor')->references($visitor)
            ->field('statusId')->equals(Session::STATUS_INVITE)
            ->getQuery()
            ->execute();
    }

}