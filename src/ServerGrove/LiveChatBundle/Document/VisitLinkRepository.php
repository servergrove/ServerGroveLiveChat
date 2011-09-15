<?php

namespace ServerGrove\LiveChatBundle\Document;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
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