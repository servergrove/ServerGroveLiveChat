<?php

namespace Application\ChatBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Description of VisitRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class VisitRepository extends DocumentRepository
{

    /**
     * @return Application\ChatBundle\Document\Visit
     */
    public function create(Visitor $visitor)
    {
        $visit = new Visit();
        $visit->setVisitor($visitor);
        $visit->setKey(md5(time() . $visitor->getAgent() . $visitor->getId()));
        #$visit->setLocalTime($localTime);

        return $visit;
    }

    /**
     * @return Application\ChatBundle\Document\Visit
     */
    public function getByKey($key, Visitor $visitor)
    {
        $visit = null;
        if (!is_null($key)) {
            $visit = $this->findOneBy(array('key' => $key));
        }

        if (!$visit) {
            $visit = $this->create($visitor);
            $this->getDocumentManager()->persist($visit);
            $this->getDocumentManager()->flush();
        }

        return $visit;
    }

}