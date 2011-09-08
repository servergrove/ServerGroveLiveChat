<?php

namespace ServerGrove\LiveChatBundle\Controller;

use ServerGrove\LiveChatBundle\Document\Visitor;
use Symfony\Component\HttpFoundation\Cookie;
use ServerGrove\LiveChatBundle\Controller\BaseController;

/**
 * Description of PublicController
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
abstract class PublicController extends BaseController
{

    /**
     * @return \ServerGrove\LiveChatBundle\Document\VisitorRepository
     */
    protected function getVisitorRepository()
    {
        return $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Visitor');
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Visitor
     */
    protected function getVisitorByKey()
    {
        $key = $this->getRequest()->cookies->get('vtrid');
        $visitor = $this->getVisitorRepository()->getByKey($key);

        if (!$visitor) {
            $visitor = $this->createVisitor();
        }

        if (!$visitor) {
            throw new \Exception("Failed to get visitor");
        }

        if (!$this->getRequest()->cookies->has('vtrid') || $key != $visitor->getKey()) {
            $this->getResponse()->headers->setCookie(new Cookie('vtrid', $visitor->getKey(), mktime(0, 0, 0, 12, 31, 2020), '/'));
        }

        return $visitor;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\VisitRepository
     */
    protected function getVisitRepository()
    {
        return $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Visit');
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\Visitor $visitor
     * @return \ServerGrove\LiveChatBundle\Document\Visit
     */
    protected function getVisitByKey(Visitor $visitor)
    {
        $key = $this->getRequest()->cookies->get('vsid');
        $visit = $this->getVisitRepository()->getByKey($key, $visitor);

        if (!$visit) {
            $visit = $this->createVisit($visitor);
        }

        if (!$this->getRequest()->cookies->has('vsid') || $key != $visit->getKey()) {
            $this->getResponse()->headers->setCookie(new Cookie('vsid', $visit->getKey(), time() + 86400, '/'));
        }

        return $visit;
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\Visitor $visitor
     * @return \ServerGrove\LiveChatBundle\Document\Visitor
     */
    protected function createVisit(Visitor $visitor)
    {
        if ('POST' == $this->getRequest()->getMethod()) {
            $lt = $this->getRequest()->request->get('lt');
            $tz = $this->getRequest()->request->get('tz');
        } else {
            $lt = $this->getRequest()->query->get('lt');
            $tz = $this->getRequest()->query->get('tz');
        }

        return $this->getVisitRepository()->create($visitor, $this->getRequest()->getClientIp(), $lt, $tz);
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Visitor
     */
    protected function createVisitor()
    {
        return $this->getVisitorRepository()->create(
                array(
                    'agent' => $this->getRequest()->server->get('HTTP_USER_AGENT'),
                    'remoteAddr' => $this->getRequest()->getClientIp(),
                    'languages' => implode(';', $this->getRequest()->getLanguages())
                )
        );
    }

}