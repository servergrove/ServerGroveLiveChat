<?php

namespace Application\ChatBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Application\ChatBundle\Document\VisitHit;
use Application\ChatBundle\Controller\BaseController;
use MongoDate;

/**
 * Chat's tracker controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class TrackController extends PublicController
{

    public function indexAction()
    {
        //$this->getResponse()->headers->set('Content-type', 'text/js');
        return $this->renderTemplate('ChatBundle:Track:index.twig.html');
    }

    public function updateAction()
    {
        $this->getResponse()->setContent(1);
        if ($this->getOperator()) {
            return $this->getResponse();
        }

        $visitor = $this->getVisitorByKey();
        $visit = $this->getVisitByKey($visitor);

        $visit->setUpdatedAt(new MongoDate(time()));
        if ($this->getRequest()->query->has('lt')) {
            $visit->setLocalTime($this->getRequest()->query->get('lt'));
        }

        $this->getDocumentManager()->getRepository('ChatBundle:Operator')->closeOldLogins();

        $this->getDocumentManager()->persist($visit);
        $this->getDocumentManager()->flush();

        if ('POST' == $this->getRequest()->getMethod()) {
            $hit = new VisitHit();
            $hit->setVisit($visit);

            $visitLink = $this->getDocumentManager()->getRepository('ChatBundle:VisitLink')->findByUrl($this->getRequest()->headers->get('Referer'));
            $hit->setVisitLink($visitLink);

            $this->getDocumentManager()->persist($visit);
            $this->getDocumentManager()->persist($hit);
            $this->getDocumentManager()->flush();

            return $this->getResponse();
        }

        $chats = $this->getDocumentManager()->getRepository('ChatBundle:Session')->getOpenInvites();

        if (count($chats) > 0) {
            /* @var $chat \Application\ChatBundle\Document\Session */
            $chat = current($chats);

            if (!$this->getHttpSession()->get('chat_invite', null)) {
                $this->getHttpSession()->set('chat_invite', $chat->getId());
            }

            $this->getResponse()->headers->set('Content-type', 'text/javascript');

            return $this->renderTemplate('ChatBundle:Track:create-invite-box.twig.js', array(
                'chat' => $chat));
        } else {
            if ($this->getHttpSession()->get('chat_invite')) {
                $this->getHttpSession()->set('chat_invite', null);

                $this->getResponse()->headers->set('Content-type', 'text/javascript');

                return $this->renderTemplate('ChatBundle:Track:close-invite-box.twig.js');
            }
        }

        return $this->getResponse();
    }

    public function statusAction($_format)
    {
        $online = $this->getDocumentManager()->getRepository('ChatBundle:Operator')->getOnlineOperatorsCount() > 0;

        return $this->renderTemplate('ChatBundle:Track:status.twig.' . $this->getRequest()->query->get('format'), array(
            'online' => $online));
    }

    public function resetAction()
    {
        $this->getResponse()->headers->setCookie(new Cookie('vtrid', null, mktime(0, 0, 0, 12, 31, 2020), '/'));
        $this->getResponse()->headers->setCookie(new Cookie('vsid', null, mktime(0, 0, 0, 12, 31, 2020), '/'));

        //return $this->forward('ChatBundle:Track:update'); # @todo Forward creates new response, so the cookies are erased
        return $this->updateAction();
    }

}
