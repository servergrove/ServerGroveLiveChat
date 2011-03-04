<?php

namespace ServerGrove\SGLiveChatBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use ServerGrove\SGLiveChatBundle\Document\VisitHit;
use ServerGrove\SGLiveChatBundle\Controller\BaseController;
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
        return $this->renderTemplate('SGLiveChatBundle:Track:index.html.twig');
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

        $this->getDocumentManager()->getRepository('SGLiveChatBundle:Operator')->closeOldLogins();

        $this->getDocumentManager()->persist($visit);
        $this->getDocumentManager()->flush();

        if ('POST' == $this->getRequest()->getMethod()) {
            $hit = new VisitHit();
            $visit->addHit($hit);

            $visitLink = $this->getDocumentManager()->getRepository('SGLiveChatBundle:VisitLink')->findByUrl($this->getRequest()->headers->get('Referer'));
            $hit->setVisitLink($visitLink);

            $this->getDocumentManager()->persist($visit);
            $this->getDocumentManager()->persist($hit);
            $this->getDocumentManager()->flush();

            return $this->getResponse();
        }

        $chats = $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->getOpenInvitesForVisitor($visitor);

        if ($chats->count() > 0) {
            /* @var $chat \ServerGrove\SGLiveChatBundle\Document\Session */
            $chat = $chats->getSingleResult();
                        

            if (!$this->getSessionStorage()->get('chat_invite', null)) {
                $this->getSessionStorage()->set('chat_invite', $chat->getId());
            }

            $this->getResponse()->headers->set('Content-type', 'text/javascript');

            return $this->renderTemplate('SGLiveChatBundle:Track:create-invite-box.js.twig', array(
                'chat' => $chat));
        } else {
            if ($this->getSessionStorage()->get('chat_invite')) {
                $this->getSessionStorage()->set('chat_invite', null);

                $this->getResponse()->headers->set('Content-type', 'text/javascript');

                return $this->renderTemplate('SGLiveChatBundle:Track:close-invite-box.js.twig');
            }
        }

        return $this->getResponse();
    }

    public function statusAction($_format)
    {
        $online = $this->getDocumentManager()->getRepository('SGLiveChatBundle:Operator')->getOnlineOperatorsCount() > 0;
        return $this->renderTemplate('SGLiveChatBundle:Track:status.' . $_format . '.twig', array(
            'online' => $online));
    }

    public function resetAction()
    {
        $this->getResponse()->headers->setCookie(new Cookie('vtrid', null, mktime(0, 0, 0, 12, 31, 2020), '/'));
        $this->getResponse()->headers->setCookie(new Cookie('vsid', null, mktime(0, 0, 0, 12, 31, 2020), '/'));

        //return $this->forward('SGLiveChatBundle:Track:update'); # @todo Forward creates new response, so the cookies are erased
        return $this->updateAction();
    }

}
