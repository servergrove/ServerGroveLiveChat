<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use MongoDate;

/**
 * Description of VisitRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class VisitRepository extends DocumentRepository
{

    public function findSlice($offset, $length)
    {
        return $this->createQueryBuilder()->skip($offset)->limit($length)->sort('createdAt', 'desc')->getQuery()->execute();
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Visit
     */
    public function create(Visitor $visitor, $remoteAddr, $localTime, $timeZone, $visitKey = null)
    {
        $visit = new Visit();
        $visit->setVisitor($visitor);

        if (is_null($visitKey)) {
            $visitKey = md5(time() . $visitor->getAgent() . $visitor->getId());
        }

        $visit->setKey($visitKey);
        $visit->setRemoteAddr($remoteAddr);
        $visit->setLocalTime($localTime);
        $visit->setLocalTimeZone($timeZone);

        return $visit;
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Visit
     */
    public function getByKey($key, Visitor $visitor)
    {
        $visit = null;
        if (!is_null($key)) {
            $visit = $this->findOneBy(array('key' => $key));
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
                    'referer' => $hits->last()->getReferer()
                ),
                'hits' => array_map(
                        function (VisitHit $hit) {
                            return array(
                                'id' => $hit->getId(),
                                'createdAt' => $hit->getCreatedAt()->format('Y-m-d H:i:s'),
                                'duration' => 0,
                                'link' => $hit->getVisitLink()->getUrl(),
                                'referer' => $hit->getReferer()
                            );
                        }, $hits->toArray(true)
                ),
                'localtime' => $visit->getLocalTime(),
                'hostname' =>  gethostbyaddr($visit->getRemoteAddr()),
                'remoteAddr' => $visit->getRemoteAddr(),
                'country' => 'unknown',
                'createdAt' => $visit->getCreatedAt()->format('Y-m-d H:i:s'),
                'lastHit' => 'lasthit',
                'duration' => $visit->getDuration()
            );
        }

        return $array;
    }

}