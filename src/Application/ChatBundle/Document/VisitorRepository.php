<?php

namespace Application\ChatBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Application\ChatBundle\Document\Visitor;

/**
 * Description of VisitRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class VisitorRepository extends DocumentRepository
{
    const REPOSITORY_NAME = 'ChatBundle:Visitor';

    public function create(array $args)
    {
        $visitor = new Visitor();
        foreach ($args as $k => $v) {
            $methodName = 'set' . \ucfirst($k);
            if (\method_exists($visitor, $methodName)) {
                \call_user_func(array($visitor, $methodName), $v);
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
     * @return Application\ChatBundle\Document\Visitor
     */
    public function getByKey($key)
    {
        $visit = null;
        if (!is_null($key)) {
            $visit = $this->findOneBy(array('key' => $key));
        }

        $visitor = null;
        if (!is_null($key)) {
            $visitor = $this->getDocumentManager()->getRepository(self::REPOSITORY_NAME)->findOneBy(array('key' => $key));
        }

        return $visitor;
    }

}