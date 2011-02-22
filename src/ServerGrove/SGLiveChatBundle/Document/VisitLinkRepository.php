<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author ismael
 */
class VisitLinkRepository extends DocumentRepository
{

    /**
     * @param string $url
     * @return VisitLink
     */
    public function findByUrl($url)
    {
        $link = $this->findOneBy(array('url' => $url));
        if (!($link instanceof VisitLink)) {
            $link = new VisitLink();
            $link->setUrl($url);
            $this->getDocumentManager()->persist($link);
            $this->getDocumentManager()->flush();
        }

        return $link;
    }

}