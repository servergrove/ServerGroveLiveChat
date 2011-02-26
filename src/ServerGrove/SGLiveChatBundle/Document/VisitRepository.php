<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;
use MongoDate;

/**
 * Description of VisitRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class VisitRepository extends DocumentRepository
{

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Visit
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
     * @return ServerGrove\SGLiveChatBundle\Document\Visit
     */
    public function getByKey($key, Visitor $visitor)
    {
        $visit = null;
        if (!is_null($key)) {
            $visit = $this->findOneBy(array(
                'key' => $key));
        }

        if (!$visit) {
            $visit = $this->create($visitor);
            $this->getDocumentManager()->persist($visit);
            $this->getDocumentManager()->flush();
        }

        return $visit;
    }

    public function getLastVisits()
    {
        return $this->createQueryBuilder()->field('updatedAt')->range(new MongoDate(time() - 200), new MongoDate(time()))->field('hits')->exists(true)->getQuery()->execute();
    }

    public function getLastVisitsArray()
    {
        $array = array();
        $visits = $this->getLastVisits();
        /* @var $visit Visit */
        foreach ($visits as $visit) {
            $hits = $visit->getHits();
            $array[] = array(
                'id' => $visit->getId(),
                'visitor' => array(
                    'id' => $visit->getVisitor()->getId(),
                    'visits' => count($visit->getVisitor()->getVisits()),
                    'languages' => $visit->getVisitor()->getLanguages(),
                    'agent' => $visit->getVisitor()->getAgent(),
                    'currentPage' => $visit->getHits()->last()->getVisitLink()->getUrl(),
                    'referer' => $hits->last()->getReferer()),
                'hits' => array_map(
                function (VisitHit $hit)
                {
                    return array(
                        'id' => $hit->getId(),
                        'createdAt' => $hit->getCreatedAt()->format('Y-m-d H:i:s'),
                        'duration' => 0,
                        'link' => $hit->getVisitLink()->getUrl(),
                        'referer' => $hit->getReferer());
                }, $hits->toArray(true)),
                'localtime' => date('r', (int) $visit->getLocalTime()),
                'hostname' => /* gethostbyaddr($visit->getRemoteAddr()) */'Unknown',
                'remoteAddr' => $visit->getRemoteAddr(),
                'country' => 'unknown',
                'createdAt' => $visit->getCreatedAt()->format('Y-m-d H:i:s'),
                'lastHit' => 'lasthit',
                'duration' => time() - $visit->getCreatedAt()->format('U'));
        }

        return $array;
    }

}