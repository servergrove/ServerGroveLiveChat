<?php

namespace Application\ChatBundle\Controller;

use Application\ChatBundle\Document\Visitor;
use Symfony\Component\HttpFoundation\Cookie;
use Application\ChatBundle\Controller\BaseController;

/**
 * Description of PublicController
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
abstract class PublicController extends BaseController
{

    /**
     * @return Application\ChatBundle\Document\VisitorRepository
     */
    protected function getVisitorRepository()
    {
        return $this->getDocumentManager()->getRepository('ChatBundle:Visitor');
    }

    /**
     * @return Application\ChatBundle\Document\Visitor
     */
    protected function getVisitorByKey()
    {
        $key = $this->getRequest()->cookies->get('vtrid');
        $visitor = $this->getVisitorRepository()->getByKey($key);

        if (!$visitor) {
            $visitor = $this->createVisitor();
            $this->getVisitorRepository()->persist($visitor);
        }

        if ($visitor && !$this->getRequest()->cookies->has('vtrid')) {
            $this->getResponse()->headers->setCookie(new Cookie('vtrid', $visitor->getKey(), mktime(0, 0, 0, 12, 31, 2020), '/'));
        }

        return $visitor;
    }

    /**
     * @return Application\ChatBundle\Document\VisitRepository
     */
    protected function getVisitRepository()
    {
        return $this->getDocumentManager()->getRepository('ChatBundle:Visit');
    }

    /**
     * @return Application\ChatBundle\Document\Visit
     */
    protected function getVisitByKey(Visitor $visitor)
    {
        $key = $this->getRequest()->cookies->get('vsid');
        $visit = $this->getVisitRepository()->getByKey($key, $visitor);

        if ($visit && !$this->getRequest()->cookies->has('vsid')) {
            $this->getResponse()->headers->setCookie(new Cookie('vsid', $visit->getKey(), time() + 86400, '/'));
        }

        return $visit;
    }

    /**
     * @return Application\ChatBundle\Document\Visitor
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