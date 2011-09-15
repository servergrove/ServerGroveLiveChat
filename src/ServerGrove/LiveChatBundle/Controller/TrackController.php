<?php

namespace ServerGrove\LiveChatBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use ServerGrove\LiveChatBundle\Document\VisitHit;
use ServerGrove\LiveChatBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chat's tracker controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class TrackController extends PublicController
{

    /**
     * @Route("", name="sglc_track_index")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Template("ServerGroveLiveChatBundle:Track:api.js.twig")
     */
    public function apiAction()
    {
        return array('hostname' => $this->getRequest()->server->get('HTTP_HOST'));
    }

    /**
     * @Route("/update", name="sglc_track_updater")
     */
    public function updateAction()
    {
        $response = new Response(1);
        $response->headers->set('Content-type', 'text/plain');

        if ($this->getOperator()) {
            return $response;
        }

        $visitor = $this->getVisitorByKey();
        $visit = $this->getVisitByKey($visitor);

        $visit->setUpdatedAt(new \MongoDate(time()));

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
            $response->setContent('SGChatTracker.loadUpdater();');
            $response->headers->set('Content-type', 'text/javascript');
        }

        $firstRequest = 'POST' == $this->getRequest()->getMethod() || ($this->getRequest()->query->has('first') && 1 == $this->getRequest()->get('first'));

        if ($firstRequest) {
            $hit = new VisitHit();
            $visit->addHit($hit);

            $visitLink = $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:VisitLink')->findByUrl($this->getRequest()->headers->get('Referer'));
            $hit->setVisitLink($visitLink);

            $this->getDocumentManager()->persist($visit);
            $this->getDocumentManager()->persist($hit);
            $this->getDocumentManager()->flush();

            return $response;
        }

        $chats = $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Session')->getOpenInvitesForVisitor($visitor);

        if ($chats->count() > 0) {
            /* @var $chat \ServerGrove\LiveChatBundle\Document\Session */
            $chat = $chats->getSingleResult();


            if (!$this->getSessionStorage()->get('chat_invite', null)) {
                $this->getSessionStorage()->set('chat_invite', $chat->getId());
            }

            $response->headers->set('Content-type', 'text/javascript');

            return $this->render('ServerGroveLiveChatBundle:Track:create-invite-box.js.twig', array('chat' => $chat), $response);
        } else {
            if ($this->getSessionStorage()->get('chat_invite')) {
                $this->getSessionStorage()->set('chat_invite', null);

                return $this->render('ServerGroveLiveChatBundle:Track:close-invite-box.js.twig');
            }
        }

        return $response;
    }

    /**
     * @Route("/status.{_format}", name="sglc_track_status", defaults={"_format"="html"})
     * @Template
     */
    public function statusAction($_format)
    {
        $online = $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Operator')->getOnlineOperatorsCount() > 0;

        return array('online' => $online);
    }

    /**
     * @Route("/update", name="sglc_track_reset")
     */
    public function resetAction()
    {
        $this->get('livechat.cookies')->set(new Cookie('vtrid', null, mktime(0, 0, 0, 12, 31, 2020), '/'));
        $this->get('livechat.cookies')->set(new Cookie('vsid', null, mktime(0, 0, 0, 12, 31, 2020), '/'));

        return $this->updateAction();
    }

}
