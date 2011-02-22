<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Description of VisitRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class VisitorRepository extends DocumentRepository
{

    const REPOSITORY_NAME = 'SGLiveChatBundle:Visitor';

    public function create(array $args)
    {
        $visitor = new Visitor();
        foreach ($args as $k => $v) {
            $methodName = 'set' . ucfirst($k);
            if (method_exists($visitor, $methodName)) {
                call_user_func(array(
                    $visitor,
                    $methodName), $v);
            }
        }
        $visitor->setKey(md5(time() . $visitor->getAgent() . rand(0, 100)));

        return $visitor;
    }

    public function persist(Visitor $visitor)
    {
        $this->getDocumentManager()->persist($visitor);
        $this->getDocumentManager()->flush();
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Visitor
     */
    public function getByKey($key)
    {
        $visitor = null;
        if (!is_null($key)) {
            $visitor = $this->findOneBy(array(
                'key' => $key));
        }
        return $visitor;
    }

}