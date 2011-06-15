<?php

namespace ServerGrove\LiveChatBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use ServerGrove\LiveChatBundle\Document\VisitHit;
use ServerGrove\LiveChatBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MongoDate;

/**
 * Chat's tracker controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class TrackController extends PublicController
{

    /**
     * @Route("/js/sglivechat-tracker", name="sglc_track_index")
     */
    public function indexAction()
    {
        return $this->renderTemplate('ServerGroveLiveChatBundle:Track:index.html.twig');
    }

    /**
     * @Template("ServerGroveLiveChatBundle:Track:api.js.twig")
     */
    public function apiAction()
    {
        return array(
            'hostname' => $this->getRequest()->server->get('HTTP_HOST')
        );
    }

    /**
     * @Route("/js/sglivechat-tracker/update", name="sglc_track_updater")
     */
    public function updateAction()
    {
        $this->getResponse()->setContent('1');
        $this->getResponse()->headers->set('Content-type', 'text/plain');
        if ($this->getOperator()) {
            return $this->getResponse();
        }

        $visitor = $this->getVisitorByKey();
        $visit = $this->getVisitByKey($visitor);

        $visit->setUpdatedAt(new MongoDate(time()));

        if ('POST' == $this->getRequest()->getMethod()) {
            if ($this->getRequest()->request->has('lt')) {
                $visit->setLocalTime($this->getRequest()->request->get('lt'));
            }
        } else {
            if ($this->getRequest()->query->has('lt')) {
                $visit->setLocalTime($this->getRequest()->query->get('lt'));
            }
        }

        $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Operator')->closeOldLogins();

        $this->getDocumentManager()->persist($visit);
        $this->getDocumentManager()->flush();

        $remote = ($this->getRequest()->query->has('remote') && 1 == $this->getRequest()->get('remote'));
        if ($remote) {
            $this->getResponse()->setContent('SGChatTracker.loadUpdater();');
            $this->getResponse()->headers->set('Content-type', 'text/javascript');
        }

        $firstRequest = 'POST' == $this->getRequest()->getMethod() || ($this->getRequest()->query->has('first') && 1 == $this->getRequest()->get('first'));
        ;
        if ($firstRequest) {
            $hit = new VisitHit();
            $visit->addHit($hit);

            $visitLink = $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:VisitLink')->findByUrl($this->getRequest()->headers->get('Referer'));
            $hit->setVisitLink($visitLink);

            $this->getDocumentManager()->persist($visit);
            $this->getDocumentManager()->persist($hit);
            $this->getDocumentManager()->flush();

            return $this->getResponse();
        }

        $chats = $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Session')->getOpenInvitesForVisitor($visitor);

        if ($chats->count() > 0) {
            /* @var $chat \ServerGrove\LiveChatBundle\Document\Session */
            $chat = $chats->getSingleResult();


            if (!$this->getSessionStorage()->get('chat_invite', null)) {
                $this->getSessionStorage()->set('chat_invite', $chat->getId());
            }

            $this->getResponse()->headers->set('Content-type', 'text/javascript');

            return $this->renderTemplate('ServerGroveLiveChatBundle:Track:create-invite-box.js.twig', array(
                'chat' => $chat));
        } else {
            if ($this->getSessionStorage()->get('chat_invite')) {
                $this->getSessionStorage()->set('chat_invite', null);

                $this->getResponse()->headers->set('Content-type', 'text/javascript');

                return $this->renderTemplate('ServerGroveLiveChatBundle:Track:close-invite-box.js.twig');
            }
        }

        return $this->getResponse();
    }

    /**
     * @Route("/js/sglivechat-tracker/status.{_format}", name="sglc_track_status", defaults={"_format"="html"})
     */
    public function statusAction($_format)
    {
        $online = $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Operator')->getOnlineOperatorsCount() > 0;
        return $this->renderTemplate('ServerGroveLiveChatBundle:Track:status.' . $_format . '.twig', array('online' => $online));
    }

    /**
     * @Route("/js/sglivechat-tracker/update", name="sglc_track_reset")
     */
    public function resetAction()
    {
        $this->getResponse()->headers->setCookie(new Cookie('vtrid', null, mktime(0, 0, 0, 12, 31, 2020), '/'));
        $this->getResponse()->headers->setCookie(new Cookie('vsid', null, mktime(0, 0, 0, 12, 31, 2020), '/'));

        //return $this->forward('ServerGroveLiveChatBundle:Track:update'); # @todo Forward creates new response, so the cookies are erased
        return $this->updateAction();
    }

}
