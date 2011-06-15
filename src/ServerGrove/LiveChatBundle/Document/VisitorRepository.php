<?php

namespace ServerGrove\LiveChatBundle\Document;

/**
 * Description of VisitRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class VisitorRepository extends DocumentRepository
{
    const REPOSITORY_NAME = 'ServerGroveLiveChatBundle:Visitor';

    public function findSlice($offset, $length)
    {
        return $this->createQueryBuilder()->skip($offset)->limit($length)->sort('createdAt', 'desc')->getQuery()->execute();
    }

    public function create(array $args)
    {
        $visitor = new Visitor();
        foreach ($args as $k => $v) {
            $methodName = 'set' . ucfirst($k);
            if (method_exists($visitor, $methodName)) {
                call_user_func(array($visitor, $methodName), $v);
            }
        }
        $visitor->setKey(md5(time() . $visitor->getAgent() . rand(0, 100)));

        $this->getDocumentManager()->persist($visitor);
        $this->getDocumentManager()->flush();
        
        return $visitor;
    }

    public function persist(Visitor $visitor)
    {
        $this->getDocumentManager()->persist($visitor);
        $this->getDocumentManager()->flush();
    }

    /**
     * @return ServerGrove\LiveChatBundle\Document\Visitor
     */
    public function getByKey($key)
    {
        $visitor = null;
        if (!is_null($key)) {
            $visitor = $this->findOneBy(array('key' => $key));
        }
        return $visitor;
    }

}