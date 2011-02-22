<?php

namespace ServerGrove\SGLiveChatBundle\Controller;

use ServerGrove\SGLiveChatBundle\Document\Visitor;
use Symfony\Component\HttpFoundation\Cookie;
use ServerGrove\SGLiveChatBundle\Controller\BaseController;

/**
 * Description of PublicController
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
abstract class PublicController extends BaseController
{

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\VisitorRepository
     */
    protected function getVisitorRepository()
    {
        return $this->getDocumentManager()->getRepository('SGLiveChatBundle:Visitor');
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Visitor
     */
    protected function getVisitorByKey()
    {
        $key = $this->getRequest()->cookies->get('vtrid');
        $visitor = $this->getVisitorRepository()->getByKey($key);

        if (!$visitor) {
            $visitor = $this->createVisitor();
            $this->getVisitorRepository()->persist($visitor);
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
     * @return ServerGrove\SGLiveChatBundle\Document\VisitRepository
     */
    protected function getVisitRepository()
    {
        return $this->getDocumentManager()->getRepository('SGLiveChatBundle:Visit');
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Visit
     */
    protected function getVisitByKey(Visitor $visitor)
    {
        $key = $this->getRequest()->cookies->get('vsid');
        $visit = $this->getVisitRepository()->getByKey($key, $visitor);

        if (!$visit) {
            throw new \Exception("Failed to get visit");
        }

        if (!$this->getRequest()->cookies->has('vsid') || $key != $visit->getKey()) {
            $this->getResponse()->headers->setCookie(new Cookie('vsid', $visit->getKey(), time() + 86400, '/'));
        }

        return $visit;
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Visitor
     */
    protected function createVisitor()
    {
        return $this->getVisitorRepository()->create(
                array(
                    'agent' => $this->getRequest()->server->get('HTTP_USER_AGENT'),
                    'remoteAddr' => $this->getRequest()->getClientIp(),
                    'languages' => implode(';', $this->getRequest()->getLanguages())));
    }
}